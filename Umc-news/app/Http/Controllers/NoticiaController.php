<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use App\Models\Categoria;
use App\Services\Post\PostService;
use Illuminate\Http\Request;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use RuntimeException;
use Throwable;

class NoticiaController extends Controller
{
    public function __construct(protected PostService $service)
    {
    }

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

        // Secciones que muestra la portada y cuántas notas toma cada una
        $definicionSecciones = [
            ['nombre' => 'Deportes', 'limite' => 2],
            ['nombre' => 'Cultura', 'limite' => 1],
            ['nombre' => 'Economia', 'limite' => 4],
            ['nombre' => 'Campus', 'limite' => 2],
        ];

        // Una sola consulta para todas las secciones, agrupadas en memoria
        $publicadas = Noticia::publicados()
            ->with('categoria')
            ->latest('fecha_publicacion')
            ->get()
            ->groupBy(fn ($noticia) => $noticia->categoria->nombre ?? 'General');

        // Cada sección lleva su categoría (para el enlace "Ver más") y sus notas
        $secciones = collect($definicionSecciones)->map(function ($seccion) use ($publicadas) {
            $grupo = $publicadas->get($seccion['nombre'], collect());

            return [
                'nombre' => $seccion['nombre'],
                'categoria' => $grupo->first()?->categoria,
                'noticias' => $grupo->take($seccion['limite'])->values(),
            ];
        });

        return view('home', compact('noticiaPrincipal', 'secciones'));
    }

    public function index()
    {
        $noticias = $this->service->getall();

        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        $noticia = new Noticia();
        $categorias = Categoria::all();

        return view('admin.noticias.form', compact('noticia', 'categorias'));
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();
        $data['es_principal'] = $request->boolean('es_principal');

        if ($request->hasFile('imagen_noticia')) {
            $data['imagen_noticia'] = $this->procesarImagen($request->file('imagen_noticia'));
        }

        DB::transaction(function () use ($data) {
            // Solo puede haber una noticia principal
            if ($data['es_principal']) {
                Noticia::where('es_principal', true)->update(['es_principal' => false]);
            }

            $this->service->create($data);
        });

        return redirect()->route('admin.noticias.index')->with('message', 'La noticia se publicó correctamente.');
    }

    public function edit(int $id)
    {
        $noticia = $this->service->find($id);
        $categorias = Categoria::all();

        return view('admin.noticias.form', compact('noticia', 'categorias'));
    }

    public function update(UpdatePostRequest $request, int $id)
    {
        $data = $request->validated();
        $noticiaActual = $this->service->find($id);

        $data['es_principal'] = $request->boolean('es_principal');
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

                if (! $this->service->update($id, $data)) {
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
        $noticia = $this->service->find($id);

        // Al eliminar la noticia también borramos su foto físicamente
        if ($noticia && $noticia->imagen_noticia) {
            Storage::disk('public')->delete($noticia->imagen_noticia);
        }

        $this->service->delete($id);

        return redirect()->route('admin.noticias.index')->with('message', 'La noticia fue eliminada.');
    }

    public function show(string $slug)
    {
        $noticia = Noticia::publicados()
            ->where('slug', $slug)
            ->with(['comentarios', 'categoria'])
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

        // Relacionadas: mismas que ya no son borrador, más recientes primero
        $noticias_relacionadas = Noticia::publicados()
            ->where('id_noticia', '!=', $noticia->id_noticia)
            ->latest('fecha_publicacion')
            ->take(3)
            ->get();

        return view('noticia.cuerpo-noticia', compact('noticia', 'noticias_relacionadas'));
    }

    public function categoria(int $id)
    {
        $categoria = Categoria::findOrFail($id);

        $noticias = Noticia::publicados()
            ->where('id_categoria', $id)
            ->latest('fecha_publicacion')
            ->paginate(10);

        return view('noticia.categoria', compact('categoria', 'noticias'));
    }

    private function procesarImagen($file)
    {
        $imagenProcesada = Image::make($file)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio(); // no deforma la imagen
                $constraint->upSize();      // no agranda si ya es más pequeña de 1200px
            })
            ->encode('webp', 80);

        $nombreArchivo = Str::uuid() . '.webp';
        $rutaCompleta = 'noticias/' . date('Y/m') . '/' . $nombreArchivo;

        Storage::disk('public')->put($rutaCompleta, $imagenProcesada);

        return $rutaCompleta;
    }
}
