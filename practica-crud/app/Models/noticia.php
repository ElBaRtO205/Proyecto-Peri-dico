<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
protected $casts = [
        'fecha_publicacion' => 'datetime',
    ];
    public const PAGINATE = 4;

public function categoria(): BelongsTo
    {
        // Revisa si tu columna en la base de datos se llama 'categoria_id'. 
        // Si se llama diferente (por ejemplo, 'id_categoria'), pasala como segundo parametro:
       
        return $this->belongsTo(Categoria::class,'id_categoria');
    }

    /**
     * Relacion: Una noticia pertenece a un autor
     */
    public function autor(): BelongsTo
    {
        // Al igual que con categoria, si tu columna no es 'autor_id', especificala aquí
        return $this->belongsTo(Autor::class,'id_autor');
    }
public function comentarios()
{
    return $this->hasMany(Comentario::class, 'id_noticia', 'id_noticia')->latest();
}

}

