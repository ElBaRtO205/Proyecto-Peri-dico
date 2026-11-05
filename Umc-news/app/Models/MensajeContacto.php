<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MensajeContacto extends Model
{
    use HasFactory;

    protected $table = 'mensajes_contacto';

    protected $primaryKey = 'id_mensaje';

    protected $fillable = [
        'tipo',
        'nombre',
        'email',
        'asunto',
        'mensaje',
        'persona_recomendada',
        'enlace_persona',
        'leido',
    ];

    protected $casts = [
        'leido' => 'boolean',
    ];

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'podcast' => 'Recomendación para podcast',
            'contacto' => 'Contacto general',
            default => 'Sugerencia de noticia',
        };
    }
}
