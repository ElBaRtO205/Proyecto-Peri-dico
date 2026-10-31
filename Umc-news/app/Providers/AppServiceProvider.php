<?php

namespace App\Providers;

use App\Models\Categoria;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Las categorías casi no cambian, así que las cacheamos en vez de
        // disparar una consulta a la BD en cada petición.
        $categorias = Cache::remember('categorias.activas', now()->addHours(6), function () {
            if (! Schema::hasTable('categorias')) {
                return collect();
            }

            return Categoria::orderBy('nombre')->get();
        });

        view()->share('categorias', $categorias);
    }
}
