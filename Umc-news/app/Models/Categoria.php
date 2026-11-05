<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $primaryKey = 'id_categoria';

    public $timestamps = false;

    protected $fillable = ['nombre', 'slug', 'descripcion'];

    protected static function booted(): void
    {
        static::saving(function (Categoria $categoria) {
            $base = Str::slug($categoria->nombre) ?: 'categoria-'.($categoria->id_categoria ?? 'nueva');

            if (! $categoria->slug || $categoria->isDirty('nombre')) {
                $categoria->slug = static::slugUnico($base, $categoria->id_categoria);
            }
        });
    }

    public static function slugUnico(string $base, ?int $ignorarId = null): string
    {
        $slug = $base;
        $contador = 1;

        $existe = fn (string $candidato) => static::where('slug', $candidato)
            ->when($ignorarId, fn ($query) => $query->where('id_categoria', '!=', $ignorarId))
            ->exists();

        while ($existe($slug)) {
            $slug = $base.'-'.$contador++;
        }

        return $slug;
    }

    /**
     * Las URL públicas de las categorías usan el slug.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function noticias(): HasMany
    {
        return $this->hasMany(Noticia::class, 'id_categoria', 'id_categoria');
    }
}
