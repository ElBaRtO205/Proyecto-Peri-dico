<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Noticia extends Model
{
    use HasFactory;

    protected $table = 'noticias';
    protected $primaryKey = 'id_noticia';

    public $timestamps = false;

    protected $fillable = [
        'id_categoria',
        'autor',
        'id_usuario_admin',
        'titulo',
        'slug',
        'contenido',
        'fecha_publicacion',
        'es_principal',
        'imagen_noticia',
        'status',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'es_principal' => 'boolean',
    ];

    public const PAGINATE = 4;

    /**
     * Al guardar, genera (o regenera) el slug a partir del título.
     * Así el enlace público siempre es limpio y legible.
     */
    protected static function booted(): void
    {
        static::saving(function (Noticia $noticia) {
            $base = Str::slug($noticia->titulo) ?: 'noticia-' . ($noticia->id_noticia ?? 'nueva');

            // Solo regenera si no hay slug o si el título cambió
            if (! $noticia->slug || $noticia->isDirty('titulo')) {
                $noticia->slug = static::slugUnico($base, $noticia->id_noticia);
            }
        });
    }

    /**
     * Devuelve un slug sin chocar con otras noticias (mismo título).
     */
    public static function slugUnico(string $base, ?int $ignorarId = null): string
    {
        $slug = $base;
        $contador = 1;

        $existe = fn (string $candidato) => static::where('slug', $candidato)
            ->when($ignorarId, fn ($q) => $q->where('id_noticia', '!=', $ignorarId))
            ->exists();

        while ($existe($slug)) {
            $slug = $base . '-' . $contador++;
        }

        return $slug;
    }

    /**
     * Las rutas públicas usan el slug como identificador.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Noticias visibles para el público (nunca borradores).
     */
    public function scopePublicados(Builder $query): Builder
    {
        return $query->where('status', 'publicado');
    }

    /**
     * Una noticia pertenece a una categoría.
     */
    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'id_categoria', 'id_categoria');
    }

    /**
     * Una noticia tiene muchos comentarios.
     */
    public function comentarios(): HasMany
    {
        return $this->hasMany(Comentario::class, 'id_noticia', 'id_noticia')->latest();
    }
}
