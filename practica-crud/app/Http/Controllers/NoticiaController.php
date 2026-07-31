<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Post\CreatePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Services\Post\PostService;
use App\Models\Noticia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class NoticiaController extends Controller
{
    public function __construct(protected PostService $Service)
    {
    }

  public function home()
{
    // 1. Traemos la noticia principal SOLO si NO es un borrador
    $noticiaPrincipal = \App\Models\Noticia::where('es_principal', 1)
        ->where('status', '!=', 'borrador')
        ->first();

    // 2. Traemos las noticias excluyendo los borradores
    $noticias = \App\Models\Noticia::with(['categoria', 'autor'])
        ->where('status', '!=', 'borrador')
        ->orderBy('fecha_publicacion', 'desc')
        ->get();

    // Enviamos ambas variables a la vista home
    return view('home', compact('noticiaPrincipal', 'noticias'));
}

    public function index()
    {
        $noticias = $this->Service->getall();
        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        $autores = \App\Models\Autor::all();
        $noticia = new \App\Models\Noticia();
        $categorias = \App\Models\Categoria::all();    
        return view('admin.noticias.form', compact('noticia', 'categorias', 'autores'));
    }

    public function store(CreatePostRequest $request)
    {
        $data = $request->validated();

        $data['es_principal'] = $request->boolean('es_principal');

        // Procesamos la imagen si viene en la peticion
        if ($request->hasFile('imagen_noticia')) {
            $data['imagen_noticia'] = $this->procesarImagen($request->file('imagen_noticia'));
        }


            // Usamos la transacción para asegurar que todo cambie al mismo tiempo
    \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
        // Si esta noticia es la principal, quitamos el puesto a la anterior
        if ($data['es_principal']) {
            \App\Models\Noticia::where('es_principal', 1)->update(['es_principal' => 0]);
        }
        

        $this->Service->create($data);
        });

        return redirect()->route('admin.noticias.index')->with('message', 'se creo tu mamada de noticia bro');
    }

    public function edit(int $id)
    {
        $noticia = $this->Service->find($id);
        $categorias = \App\Models\Categoria::all();
        $autores = \App\Models\Autor::all();
        return view('admin.noticias.form', compact('noticia', 'categorias', 'autores'));
    }

    public function update(UpdatePostRequest $request, int $id)
    {
        $data = $request->validated();
        $noticiaActual = $this->Service->find($id);

        $data['es_principal'] = $request->boolean('es_principal');

        if ($request->hasFile('imagen_noticia')) {
            //  Borramos la foto anterior del disco para no acumular basura
            if ($noticiaActual->imagen_noticia) {
                Storage::disk('public')->delete($noticiaActual->imagen_noticia);
            }
            //  Procesamos y ponemos la nueva foto
            $data['imagen_noticia'] = $this->procesarImagen($request->file('imagen_noticia'));
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($id, $data) {
        // Si el editor la marcó como principal, reseteamos las OTRAS noticias
        if ($data['es_principal']) {
            \App\Models\Noticia::where('es_principal', 1)
                ->where('id_noticia', '!=', $id) // No tocamos esta misma noticia en el reset
                ->update(['es_principal' => 0]);
        }
        });

        $this->Service->update($id, $data);
        return redirect()->route('admin.noticias.index')->with('message', 'se actualizo tu mamada de noticia bro');
    }

    public function destroy(int $id)
    {
        $noticia = $this->Service->find($id);
        
        // Al eliminar la noticia tambien borramos su foto fisicamente
        if ($noticia && $noticia->imagen_noticia) {
            Storage::disk('public')->delete($noticia->imagen_noticia);
        }

        $this->Service->delete($id);
        return redirect()->route('admin.noticias.index')->with('message', 'se elimino tu mamada de noticia bro');
    }

       private function procesarImagen($file)
    {
        $imagenProcesada = Image::make($file)
            ->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio(); // la ia me dijo que esto era pa que no se deforme la imagen
                $constraint->upSize();      // y este es pa evita que si la imagen es más pequeña de 1200px se estire y se pixele
            })
            ->encode('webp', 80); // Convierte a WebP con calidad del 80%

        // Generamos un nombre unico y la estructura de carpetas por año/mes
        $nombreArchivo = Str::uuid() . '.webp';
        $rutaCompleta = 'noticias/' . date('Y/m') . '/' . $nombreArchivo;

        // Guardamos los datos procesados en el disco publico en la carpeta de public/storage/noticias
        Storage::disk('public')->put($rutaCompleta, $imagenProcesada);

        return $rutaCompleta;
    }

public function show(int $id)
{
    //traemos la noticia con los comentarios
$noticia = Noticia::with('comentarios')->where('status', '!=', 'borrador')->findOrFail($id);

    // Retornamos la nueva vista que crearemos en el paso 4
    return view('noticia.show', compact('noticia'));
}

public function categoria(int $id)
{
    // 1. Buscamos la categoria o lanzamos un 404 si no existe
    $categoria = \App\Models\Categoria::findOrFail($id);

    // 2. Traemos las noticias de esta categoria, paginadas de 10 en 10
    $noticias = \App\Models\Noticia::where('id_categoria', $id)
        ->where('status', '!=', 'borrador')
        ->orderBy('fecha_publicacion', 'desc')
        ->paginate(10);

    // 3. Retornamos una vista exclusiva para las secciones
    return view('noticia.categoria', compact('categoria', 'noticias'));
}

}