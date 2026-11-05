<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Susurro extends Model
{
    protected $table = 'susurros';

    protected $primaryKey = 'id_susurro';

    public $timestamps = false;

    protected $fillable = ['apodo', 'contenido', 'categoria_viento', 'soplos', 'fecha_publicacion'];

    protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];
}
