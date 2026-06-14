<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Post\CreatePostRequest;
use App\Services\Post\PostService;
use App\Models\noticia;
use App\Http\Requests\Post\UpdatePostRequest;

class NoticiaController extends Controller
{
    public function __construct(protected PostService $Service)
    {
    }

    public function home()
    {
            // Buscamos todas las noticias paginadas y las guardamos en una variable
            $noticias = $this->Service->getall();

        // Enviamos las noticias a la vista 'home' (home.blade.php)
        return view('home', compact('noticias'));
    }

    public function index()
    {
        $noticias = $this->Service->getall();

        return view('noticia.index', compact('noticias'));
    }

    public function create()
    {
        $autores = \App\Models\Autor::all();
        $noticia = new \App\Models\Noticia();
       $categorias = \App\Models\Categoria::all();    
       // return view('noticia.form', ['noticia' => new noticia()]);
       return view('noticia.form', compact('noticia', 'categorias', 'autores'));
    }

    public function store(CreatePostRequest $request)
    {

     if ($request->hasFile('imagen_noticia')) {
        // Guarda la foto en storage/app/public/noticias y obtiene la ruta corta
        $data['imagen_noticia'] = $request->file('imagen_noticia')->store('noticias', 'public');
        };
        $this->Service->create($request->validated());
        $request->validated([

        'titulo' => 'required',
        'id_categoria' => 'required', //se coloco esta validacion PORQUE EL DE BASE DE DATOS hizo que la categoria fuera obligatoria, gracias nay, me niego a crear otra base 

        ]); 
        return redirect()->route('noticia.index')->with('message', 'se creo tu mamada de noticia bro');
    }

    public function edit(int $id)
    {
        $noticia = $this->Service->find($id);

        $categorias = \App\Models\Categoria::all();
        $autores = \App\Models\Autor::all();
        return view('noticia.form', compact('noticia', 'categorias', 'autores'));
    }

    public function update(UpdatePostRequest $request, int $id)
    {
        $this->Service->update($id, $request->validated());
        return redirect()->route('noticia.index')->with('message', 'se actualizo tu mamada de noticia bro');
    }

    public function destroy(int $id)
    {
        $this->Service->delete($id);
        return redirect()->route('noticia.index')->with('message', 'se elimino tu mamada de noticia bro');
    }
}
