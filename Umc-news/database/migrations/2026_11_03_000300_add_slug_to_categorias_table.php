<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('slug', 120)->nullable()->unique()->after('nombre');
        });

        $usados = [];

        DB::table('categorias')->orderBy('id_categoria')->each(function ($categoria) use (&$usados) {
            $base = Str::slug($categoria->nombre) ?: 'categoria-'.$categoria->id_categoria;
            $slug = $base;
            $contador = 1;

            while (in_array($slug, $usados, true)) {
                $slug = $base.'-'.$contador++;
            }

            DB::table('categorias')
                ->where('id_categoria', $categoria->id_categoria)
                ->update(['slug' => $slug]);

            $usados[] = $slug;
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
