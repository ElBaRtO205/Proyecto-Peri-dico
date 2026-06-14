<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
     Schema::create('usuarios', function (Blueprint $table) {
        $table->id('id_usuario'); // PK
        $table->string('nombre_completo', 150);
        $table->string('usuario', 50)->unique();
        $table->string('email', 100)->unique();
        $table->string('contrasena', 255);
        $table->enum('rol', ['Administrador', 'Editor'])->default('Administrador');
        $table->timestamp('fecha_creacion')->useCurrent();
     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
