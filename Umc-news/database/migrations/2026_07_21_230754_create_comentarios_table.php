<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');

            // Forma limpia y estándar en Laravel que asigna BIGINT UNSIGNED y crea la FK
            $table->foreignId('id_noticia')
                  ->constrained('noticias', 'id_noticia')
                  ->onDelete('cascade');

            $table->string('autor');
            $table->text('contenido');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};