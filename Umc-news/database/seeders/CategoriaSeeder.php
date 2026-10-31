<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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

        // updateOrCreate para poder re-sembrar sin chocar con el unique()
        foreach ($categorias as $cat) {
            DB::table('categorias')->updateOrInsert(
                ['nombre' => $cat['nombre']],
                ['descripcion' => $cat['descripcion']]
            );
        }

        // Invalidamos el caché que lee el layout en cada petición
        Cache::forget('categorias.activas');
    }
}
