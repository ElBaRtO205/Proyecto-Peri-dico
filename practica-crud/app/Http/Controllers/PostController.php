<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return redirect()->route('noticia.index');
    }

    public function create()
    {
        return redirect()->route('noticia.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('noticia.index');
    }

    public function edit($id)
    {
        return redirect()->route('noticia.edit', $id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('noticia.index');
    }

    public function destroy($id)
    {
        return redirect()->route('noticia.index');
    }
}
