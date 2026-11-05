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
        Schema::create('susurros', function (Blueprint $table) {
            $table->id('id_susurro');
            $table->string('apodo', 40)->default('Brisa Anónima');
            $table->text('contenido');
            $table->string('categoria_viento', 30)->default('Confesión');
            $table->unsignedInteger('soplos')->default(0);
            $table->timestamp('fecha_publicacion')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('susurros');
    }
};
