<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Comentario;

class ComentarioController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'autor' => 'required|string|max:100',
            'contenido' => 'required|string|max:1000',
        ]);

        Comentario::create([
            'id_noticia' => $id,
            'autor' => $request->autor,
            'contenido' => $request->contenido,
        ]);

        return back()->with('exito', '¡Tu comentario ha sido publicado!');
    }
}