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

    protected $fillable = [
        'id_categoria',
        'autor',
        'id_usuario_admin',
        'titulo',
        'slug',
        'contenido',
        'fecha_publicacion',
        'programada_para',
        'es_principal',
        'imagen_noticia',
        'status',
    ];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
        'programada_para' => 'datetime',
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
            $base = Str::slug($noticia->titulo) ?: 'noticia-'.($noticia->id_noticia ?? 'nueva');

            // Solo regenera si no hay slug o si el título cambió
            if (! $noticia->slug || $noticia->isDirty('titulo')) {
                $noticia->slug = static::slugUnico($base, $noticia->id_noticia);
            }

            // La noticia principal siempre debe estar visible. Si se manda a
            // borrador o se programa para el futuro, deja de ser candidata a
            // portada. Una programada ya vencida sí puede ser principal.
            $programadaAlFuturo = $noticia->status === 'programado'
                && (! $noticia->programada_para || $noticia->programada_para->isFuture());

            if ($noticia->status === 'borrador' || $programadaAlFuturo) {
                $noticia->es_principal = false;
            }

            // Al programar, la fecha visible se calcula desde la programación.
            if ($noticia->status === 'programado' && $noticia->programada_para) {
                $noticia->fecha_publicacion = $noticia->programada_para->toDateString();
            }
        });

        static::saved(fn (Noticia $noticia) => static::promoverPrincipalAutomatica());

        static::created(function (Noticia $noticia) {
            $noticia->registrarAuditoria('creado', [
                'despues' => $noticia->soloCamposAuditables(),
            ]);
        });

        static::updated(function (Noticia $noticia) {
            $cambios = collect($noticia->getChanges())
                ->except(['updated_at'])
                ->all();

            if ($cambios === []) {
                return;
            }

            $antes = collect(array_keys($cambios))
                ->mapWithKeys(fn (string $campo) => [$campo => $noticia->getOriginal($campo)])
                ->all();

            $noticia->registrarAuditoria('actualizado', [
                'antes' => $antes,
                'despues' => $cambios,
            ]);
        });

        // La auditoría se registra antes del DELETE para respetar la clave
        // foránea; luego la propia FK deja el id en null.
        static::deleting(function (Noticia $noticia) {
            $noticia->registrarAuditoria('eliminado', [
                'antes' => $noticia->soloCamposAuditables(),
            ]);
        });

        static::deleted(fn (Noticia $noticia) => static::promoverPrincipalAutomatica());
    }

    /**
     * Si no queda ninguna noticia principal publicada, asciende la más
     * reciente. Se ejecuta al guardar y al eliminar.
     */
    public static function promoverPrincipalAutomatica(): ?self
    {
        if (static::publicados()->where('es_principal', true)->exists()) {
            return null;
        }

        $candidata = static::publicados()
            ->where('es_principal', false)
            ->latest('fecha_publicacion')
            ->latest('id_noticia')
            ->first();

        if (! $candidata) {
            return null;
        }

        $candidata->es_principal = true;
        // saveQuietly evita reentrar en el evento saved y volver a evaluar
        // la promoción en bucle.
        $candidata->saveQuietly();

        return $candidata;
    }

    /**
     * Campos que se guardan como instantánea en la auditoría.
     */
    public function soloCamposAuditables(): array
    {
        return $this->only([
            'id_categoria',
            'autor',
            'id_usuario_admin',
            'titulo',
            'slug',
            'contenido',
            'fecha_publicacion',
            'programada_para',
            'es_principal',
            'imagen_noticia',
            'status',
        ]);
    }

    public function registrarAuditoria(string $accion, array $cambios = []): void
    {
        // Sin noticia persistida no hay a qué asociar el registro.
        if (! $this->id_noticia) {
            return;
        }

        NoticiaAuditoria::create([
            'id_noticia' => $this->id_noticia,
            'id_usuario' => auth()->id(),
            'accion' => $accion,
            'titulo' => $this->titulo,
            'cambios' => $cambios,
        ]);
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
            $slug = $base.'-'.$contador++;
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
        // Una programada se vuelve visible en cuanto llega su hora, incluso
        // si el scheduler todavía no la ha pasado a "publicado".
        return $query->where(function (Builder $subconsulta) {
            $subconsulta->where('status', 'publicado')
                ->orWhere(function (Builder $programadas) {
                    $programadas->where('status', 'programado')
                        ->whereNotNull('programada_para')
                        ->where('programada_para', '<=', now());
                });
        });
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

    public function comentariosAprobados(): HasMany
    {
        return $this->comentarios()->aprobados();
    }

    public function auditorias(): HasMany
    {
        return $this->hasMany(NoticiaAuditoria::class, 'id_noticia', 'id_noticia')
            ->latest('created_at');
    }

    /**
     * Tiempo estimado de lectura en minutos (200 palabras por minuto).
     */
    public function getTiempoLecturaAttribute(): int
    {
        $texto = trim(strip_tags((string) $this->contenido));
        $palabras = preg_match_all('/[\p{L}\p{N}]+/u', $texto);

        return max(1, (int) ceil($palabras / 200));
    }
}
