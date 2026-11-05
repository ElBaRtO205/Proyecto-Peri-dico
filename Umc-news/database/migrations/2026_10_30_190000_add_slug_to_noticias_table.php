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
        Schema::table('noticias', function (Blueprint $table) {
            $table->string('slug', 255)->nullable()->unique()->after('titulo');
        });

        // Backfill: generar slug para las noticias que ya existen
        $usados = DB::table('noticias')->pluck('slug')->filter()->flip();

        DB::table('noticias')->orderBy('id_noticia')->each(function ($noticia) use (&$usados) {
            $base = Str::slug($noticia->titulo) ?: 'noticia-'.$noticia->id_noticia;
            $slug = $base;
            $contador = 1;

            // Evita duplicados agregando un sufijo numérico
            while (isset($usados[$slug])) {
                $slug = $base.'-'.$contador++;
            }

            $usados[$slug] = true;
            DB::table('noticias')->where('id_noticia', $noticia->id_noticia)->update(['slug' => $slug]);
        });
    }

    public function down(): void
    {
        Schema::table('noticias', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn('slug');
        });
    }
};
