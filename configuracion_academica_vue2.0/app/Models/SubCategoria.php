<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategoria extends Model
{
    protected $table = 'smtr_sub_categoria'; // Nombre de la tabla en Oracle

    protected $primaryKey = 'n_id_sub_categoria'; // Clave primaria

    public $timestamps = false; // Desactiva los timestamps automáticos

    protected $fillable = [
        'n_id_categoria', // Campos que pueden ser rellenados
        'v_descripcion',
    ];
}
