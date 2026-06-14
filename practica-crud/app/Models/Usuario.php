<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
        protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
   public $timestamps = false; //al de base de datos no le gusta usar timestamps asi que se desactiva EN TODOS LOS MODELOS
    protected $fillable = [
        'nombre_completo', 
        'usuario', 
        'email', 
        'contrasena', 
        'rol'
    ];
}
