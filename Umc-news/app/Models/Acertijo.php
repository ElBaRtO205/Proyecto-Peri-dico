<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acertijo extends Model
{
    protected $table = 'acertijos';
    protected $primaryKey = 'id_acertijo';
    public $timestamps = false;

    protected $fillable = ['pregunta', 'respuesta', 'pista', 'activo'];
}