<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->timestamp('programada_para')
                ->nullable()
                ->after('fecha_publicacion');

            $table->timestamp('created_at')
                ->nullable()
                ->after('status');
            $table->timestamp('updated_at')
                ->nullable()
                ->after('created_at');
        });

        // Aprovechamos la fecha de publicación existente para no dejar las
        // noticias anteriores sin rastro temporal.
        DB::table('noticias')->update([
            'created_at' => DB::raw('fecha_publicacion'),
            'updated_at' => DB::raw('fecha_publicacion'),
        ]);

        Schema::create('noticia_auditorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_noticia')
                ->nullable()
                ->constrained('noticias', 'id_noticia')
                ->nullOnDelete()
                ->onUpdate('cascade');
            $table->foreignId('id_usuario')
                ->nullable()
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete()
                ->onUpdate('cascade');
            $table->string('accion', 20);
            $table->string('titulo', 255)->nullable();
            $table->json('cambios')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['id_noticia', 'created_at']);
            $table->index('accion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('noticia_auditorias');

        Schema::table('noticias', function (Blueprint $table) {
            $table->dropColumn(['programada_para', 'created_at', 'updated_at']);
        });
    }
};
