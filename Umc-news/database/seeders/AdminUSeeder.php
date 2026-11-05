<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! \is_string($password) || \strlen($password) < 12) {
            throw new \RuntimeException('Define ADMIN_PASSWORD with at least 12 characters before seeding.');
        }

        Usuario::updateOrCreate(
            ['email' => 'admin@umcnews.com'], // Clave para verificar si ya existe
            [
                'nombre_completo' => 'Administrador General',
                'usuario' => 'admin',
                'email' => 'admin@umcnews.com',
                'contrasena' => Hash::make($password),
                'rol' => 'Administrador',
            ]
        );
    }
}
