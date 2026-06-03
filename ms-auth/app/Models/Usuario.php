<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model {
    protected $table = 'usuarios';
    
    // Desactivar timestamps automáticos si quieres manejarlos manualmente o si el nombre no coincide
    // public $timestamps = false; 
    
    protected $fillable = [
        'nombre', 
        'correo', 
        'usuario', 
        'contrasena', 
        'rol', 
        'token', 
        'sesion_activa', 
        'estado'
    ];
}