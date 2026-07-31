<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        

        // 2. Creamos la tabla con la relación exacta
        Schema::create('comentarios', function (Blueprint $table) {
            $table->id('id_comentario');

            // int(11) CON SIGNO para hacer match exacto con tu tabla noticias
            $table->integer('id_noticia');

            $table->string('autor');
            $table->text('contenido');
            $table->timestamps();

            // Llave foránea conectada a noticias
            $table->foreign('id_noticia')
                  ->references('id_noticia')
                  ->on('noticias')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentarios');
    }
};