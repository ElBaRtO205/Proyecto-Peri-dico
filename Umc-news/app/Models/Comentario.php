<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    public const STATUS_PENDIENTE = 'pendiente';

    public const STATUS_APROBADO = 'aprobado';

    public const STATUS_RECHAZADO = 'rechazado';

    protected $table = 'comentarios';

    protected $primaryKey = 'id_comentario';

    protected $fillable = [
        'id_noticia',
        'id_usuario',
        'autor',
        'contenido',
        'status',
    ];

    public function noticia()
    {
        return $this->belongsTo(Noticia::class, 'id_noticia', 'id_noticia');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function scopeAprobados(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APROBADO);
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDIENTE);
    }
}
