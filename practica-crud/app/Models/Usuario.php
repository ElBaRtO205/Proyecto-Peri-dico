<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
class Usuario extends Authenticatable
{
    use Notifiable;
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


// Le avisa al sistema de autenticación el nombre exacto de tu columna de clave
    public function getAuthPasswordName()
    {
        return 'contrasena';
    }
}
