<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Editor'])->default('Editor')->change();
        });

        DB::table('usuarios')
            ->where('rol', 'Administrador')
            ->where('email', '!=', 'admin@umcnews.com')
            ->update(['rol' => 'Editor']);
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('rol', ['Administrador', 'Editor'])->default('Administrador')->change();
        });
    }
};