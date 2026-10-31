<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Noticia;
use Illuminate\Support\Facades\Response;

class FeedController extends Controller
{
    /**
     * Feed RSS con las noticias más recientes publicadas.
     */
    public function rss()
    {
        $noticias = Noticia::publicados()
            ->with('categoria')
            ->latest('fecha_publicacion')
            ->take(20)
            ->get();

        $xml = view('feeds.rss', compact('noticias'))->render();

        return Response::make($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Sitemap con portada, categorías y noticias publicadas.
     */
    public function sitemap()
    {
        $categorias = Categoria::orderBy('nombre')->get();

        $noticias = Noticia::publicados()
            ->latest('fecha_publicacion')
            ->get(['slug', 'fecha_publicacion']);

        $xml = view('feeds.sitemap', compact('categorias', 'noticias'))->render();

        return Response::make($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
