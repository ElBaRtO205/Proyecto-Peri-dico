<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->foreignId('id_usuario')
                ->nullable()
                ->after('id_noticia')
                ->constrained('usuarios', 'id_usuario')
                ->nullOnDelete()
                ->onUpdate('cascade');

            $table->string('status', 20)
                ->default('pendiente')
                ->after('contenido');

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('comentarios', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropConstrainedForeignId('id_usuario');
            $table->dropColumn('status');
        });
    }
};
