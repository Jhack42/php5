<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'smtr_categoria';  // Nombre de la tabla en Oracle

    protected $primaryKey = 'n_id_categoria'; // Clave primaria

    public $timestamps = false; // Desactiva los timestamps automáticos

    protected $fillable = [
        'v_titulo', // Campos que pueden ser rellenados
    ];
}
