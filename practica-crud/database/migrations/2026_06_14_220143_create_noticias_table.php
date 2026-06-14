<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('noticias', function (Blueprint $table) {
            $table->id('id_noticia'); // PK
            
           
            $table->foreignId('id_categoria')->constrained('categorias', 'id_categoria')->onUpdate('cascade');
            $table->foreignId('id_usuario_admin')->nullable()->constrained('usuarios', 'id_usuario')->onDelete('set null')->onUpdate('cascade');
            
           
            $table->string('autor', 255)->nullable(); 
            $table->string('titulo', 255);
            $table->text('contenido');
            $table->string('imagen_noticia', 255)->nullable();
            
           
            $table->date('fecha_publicacion')->default(DB::raw('(CURRENT_DATE)'));
            
            $table->boolean('es_principal')->default(false);
            $table->string('status', 255)->default('borrador'); // Nueva columna de estado
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticias');
    }
};