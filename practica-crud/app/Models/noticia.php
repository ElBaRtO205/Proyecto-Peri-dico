<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class noticia extends Model
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
        'contenido',
        'fecha_publicacion',
        'es_principal',
        'imagen_noticia',
        'status'
    ];

    public const PAGINATE = 4;
}

