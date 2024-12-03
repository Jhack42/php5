<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos_farmacia';  // Nombre de la tabla en Oracle

    protected $primaryKey = 'id';            // Clave primaria

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'stock', 'fecha_expiracion',
    ];  // Campos que pueden ser rellenados masivamente
}
