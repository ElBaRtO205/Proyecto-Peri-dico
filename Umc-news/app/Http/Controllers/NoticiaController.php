<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Acertijo;
use App\Models\Categoria;
use App\Models\Comentario;
use App\Models\Noticia;
use App\Models\NoticiaAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use RuntimeException;
use Throwable;

class NoticiaController extends Controller
{
    /**
     * Portada: noticia principal + una sección por categoría, con la
     * cantidad de notas que se muestran en cada una.
     */
    public function home()
    {
        $noticiaPrincipal = Noticia::publicados()
            ->where('es_principal', true)
            ->with('categoria')
            ->first();

        // Las secciones se generan desde las categorías reales de la base de
        // datos. Así cualquier categoría nueva aparece sin tocar el código.
        // Se consultan solo las noticias necesarias por sección, en lugar de
        // cargar todas las publicadas en memoria.
        $secciones = Categoria::query()
            ->orderBy('nombre')
            ->get()
            ->map(function (Categoria $categoria) {
                $noticias = $categoria->noticias()
                    ->publicados()
                    ->latest('fecha_publicacion')
                    ->latest('id_noticia')
                    ->take(4)
                    ->get();

                return [
                    'nombre' => $categoria->nombre,
                    'categoria' => $categoria,
                    'noticias' => $noticias,
                ];
            });

        return view('home', compact('noticiaPrincipal', 'secciones'));
    }

    public function index()
    {
        $noticias = Noticia::query()
            ->latest('fecha_publicacion')
            ->paginate(Noticia::PAGINATE);

        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        $noticia = new Noticia;
        $categorias = Categoria::all();

        return view('admin.noticias.form', compact('noticia', 'categorias'));
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();
        $data['es_principal'] = $request->boolean('es_principal');
        $data['programada_para'] = $data['status'] === 'programado'
            ? $data['programada_para']
            : null;

        if ($request->hasFile('imagen_noticia')) {
            $data['imagen_noticia'] = $this->procesarImagen($request->file('imagen_noticia'));
        }

        DB::transaction(function () use ($data) {
            // Solo puede haber una noticia principal
            if ($data['es_principal']) {
                Noticia::where('es_principal', true)->update(['es_principal' => false]);
            }

            Noticia::create($data);
        });

        return redirect()->route('admin.noticias.index')->with('message', 'La noticia se publicó correctamente.');
    }

    public function edit(int $id)
    {
        $noticia = Noticia::findOrFail($id);
        $categorias = Categoria::all();
        $auditorias = $noticia->auditorias()
            ->with('usuario')
            ->take(20)
            ->get();

        return view('admin.noticias.form', compact('noticia', 'categorias', 'auditorias'));
    }

    public function update(UpdatePostRequest $request, int $id)
    {
        $data = $request->validated();
        $noticiaActual = Noticia::findOrFail($id);

        $data['es_principal'] = $request->boolean('es_principal');
        $data['programada_para'] = $data['status'] === 'programado'
            ? $data['programada_para']
            : null;
        $imagenAnterior = $noticiaActual->imagen_noticia;
        $imagenNueva = null;

        if ($request->hasFile('imagen_noticia')) {
            $imagenNueva = $this->procesarImagen($request->file('imagen_noticia'));
            $data['imagen_noticia'] = $imagenNueva;
        }

        try {
            DB::transaction(function () use ($id, $data) {
                if ($data['es_principal']) {
                    Noticia::where('es_principal', true)
                        ->where('id_noticia', '!=', $id)
                        ->update(['es_principal' => false]);
                }

                $noticia = Noticia::findOrFail($id);
                $noticia->fill($data);

                if (! $noticia->save()) {
                    throw new RuntimeException('No se pudo actualizar la noticia.');
                }
            });
        } catch (Throwable $exception) {
            // Si falla, borramos la imagen recién procesada para no dejar basura
            if ($imagenNueva !== null) {
                Storage::disk('public')->delete($imagenNueva);
            }

            throw $exception;
        }

        if ($imagenNueva !== null && $imagenAnterior !== null) {
            Storage::disk('public')->delete($imagenAnterior);
        }

        return redirect()->route('admin.noticias.index')->with('message', 'La noticia se actualizó correctamente.');
    }

    public function destroy(int $id)
    {
        $noticia = Noticia::findOrFail($id);

        // Al eliminar la noticia también borramos su foto físicamente
        if ($noticia && $noticia->imagen_noticia) {
            Storage::disk('public')->delete($noticia->imagen_noticia);
        }

        $noticia->delete();

        return redirect()->route('admin.noticias.index')->with('message', 'La noticia fue eliminada.');
    }

    public function show(string $slug)
    {
        $noticia = Noticia::publicados()
            ->where('slug', $slug)
            ->with('categoria')
            ->first();

        // Compatibilidad con enlaces antiguos por ID numérico (/noticia/5):
        // redirigimos a la URL limpia con slug para no romper lo ya compartido.
        if (! $noticia && ctype_digit($slug)) {
            $porId = Noticia::publicados()->find((int) $slug);

            if ($porId) {
                return redirect()->route('noticia.show', $porId->slug, 301);
            }
        }

        abort_unless($noticia, 404);

        // Relacionadas: primero las de la misma categoría, para que "Sigue
        // leyendo" mantenga el contexto editorial de la noticia abierta.
        $noticias_relacionadas = Noticia::publicados()
            ->where('id_categoria', $noticia->id_categoria)
            ->where('id_noticia', '!=', $noticia->id_noticia)
            ->latest('fecha_publicacion')
            ->latest('id_noticia')
            ->take(3)
            ->get();

        // Solo se muestran comentarios aprobados, paginados para no cargar
        // todos los registros de una noticia popular.
        $comentarios = Comentario::query()
            ->where('id_noticia', $noticia->id_noticia)
            ->where('status', Comentario::STATUS_APROBADO)
            ->with('usuario')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'noticia.cuerpo-noticia',
            compact('noticia', 'noticias_relacionadas', 'comentarios')
        );
    }

    public function categoria(string $categoria)
    {
        $seccion = Categoria::where('slug', $categoria)->first();

        // Compatibilidad con enlaces antiguos por ID numérico.
        if (! $seccion && ctype_digit($categoria)) {
            $porId = Categoria::find((int) $categoria);

            if ($porId) {
                return redirect()->route('noticia.categoria', $porId, 301);
            }
        }

        abort_unless($seccion, 404);

        $noticias = $seccion->noticias()
            ->publicados()
            ->latest('fecha_publicacion')
            ->latest('id_noticia')
            ->paginate(10);

        // El acertijo solo aplica a Campus y a usuarios autenticados. Se
        // resuelve una sola vez aquí, no dentro de la vista.
        $acertijoHoy = $seccion->nombre === 'Campus' && auth()->check()
            ? Acertijo::where('activo', true)->orderByDesc('id_acertijo')->first()
            : null;

        return view('noticia.categoria', [
            'categoria' => $seccion,
            'noticias' => $noticias,
            'acertijoHoy' => $acertijoHoy,
        ]);
    }

    /**
     * Historial global de cambios, incluidas noticias ya eliminadas.
     */
    public function auditoria(Request $request)
    {
        $accion = $request->query('accion', 'todas');

        if (! in_array($accion, ['creado', 'actualizado', 'eliminado', 'todas'], true)) {
            $accion = 'todas';
        }

        $registros = NoticiaAuditoria::query()
            ->with(['usuario', 'noticia'])
            ->when($accion !== 'todas', fn ($query) => $query->where('accion', $accion))
            ->latest('created_at')
            ->paginate(25)
            ->withQueryString();

        $conteo = [
            'creado' => NoticiaAuditoria::where('accion', 'creado')->count(),
            'actualizado' => NoticiaAuditoria::where('accion', 'actualizado')->count(),
            'eliminado' => NoticiaAuditoria::where('accion', 'eliminado')->count(),
            'todas' => NoticiaAuditoria::count(),
        ];

        return view('admin.noticias.auditoria', compact('registros', 'accion', 'conteo'));
    }

    private function procesarImagen($file)
    {
        $imagenProcesada = Image::make($file)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio(); // no deforma la imagen
                $constraint->upSize();      // no agranda si ya es más pequeña de 1200px
            })
            ->encode('webp', 80);

        $nombreArchivo = Str::uuid().'.webp';
        $rutaCompleta = 'noticias/'.date('Y/m').'/'.$nombreArchivo;

        Storage::disk('public')->put($rutaCompleta, $imagenProcesada);

        return $rutaCompleta;
    }
}
