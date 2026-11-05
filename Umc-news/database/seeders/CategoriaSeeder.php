<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            [
                'nombre' => 'Economia',
                'descripcion' => 'Economía, finanzas y becas: lo que afecta el bolsillo del estudiante.',
            ],
            [
                'nombre' => 'Deportes',
                'descripcion' => 'Equipos, torneos y logros deportivos de la comunidad UMC.',
            ],
            [
                'nombre' => 'Cultura',
                'descripcion' => 'Eventos culturales, cine, música y entretenimiento.',
            ],
            [
                'nombre' => 'Campus',
                'descripcion' => 'Vida universitaria, rumores y noticias de la comunidad.',
            ],
        ];

        // Usamos el modelo para que el evento saving genere el slug público.
        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(
                ['nombre' => $categoria['nombre']],
                ['descripcion' => $categoria['descripcion']]
            );
        }

        Cache::forget('categorias.activas');
    }
}
