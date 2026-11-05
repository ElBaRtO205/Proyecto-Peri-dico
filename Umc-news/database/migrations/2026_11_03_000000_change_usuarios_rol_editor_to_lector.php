<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Primero ampliamos el enum para que MySQL acepte "Lector" sin
        // truncar los valores existentes.
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Editor', 'Lector'])
                ->default('Editor')
                ->change();
        });

        DB::table('usuarios')
            ->where('rol', 'Editor')
            ->update(['rol' => 'Lector']);

        // Después reducimos el enum a los roles reales del sistema.
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Lector'])
                ->default('Lector')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Editor', 'Lector'])
                ->default('Lector')
                ->change();
        });

        DB::table('usuarios')
            ->where('rol', 'Lector')
            ->update(['rol' => 'Editor']);

        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Editor'])
                ->default('Editor')
                ->change();
        });
    }
};
