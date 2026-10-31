<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes_contacto', function (Blueprint $table) {
            $table->id('id_mensaje');
            $table->string('tipo', 30)->default('sugerencia');
            $table->string('nombre', 120);
            $table->string('email', 150);
            $table->string('asunto', 150)->nullable();
            $table->text('mensaje');
            $table->string('persona_recomendada', 120)->nullable();
            $table->string('enlace_persona', 255)->nullable();
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_contacto');
    }
};
