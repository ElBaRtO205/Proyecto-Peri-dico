<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoticiaAuditoria extends Model
{
    protected $table = 'noticia_auditorias';

    // La tabla solo maneja created_at; la base de datos lo rellena.
    public $timestamps = false;

    protected $fillable = [
        'id_noticia',
        'id_usuario',
        'accion',
        'titulo',
        'cambios',
    ];

    protected $casts = [
        'cambios' => 'array',
        'created_at' => 'datetime',
    ];

    public function noticia(): BelongsTo
    {
        return $this->belongsTo(Noticia::class, 'id_noticia', 'id_noticia');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }
}
