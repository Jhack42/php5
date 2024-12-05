<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BakiCategoria extends Model
{
    protected $table = 'BAKI_CATEGORIAS';  // Nombre de la tabla en la base de datos

    protected $primaryKey = 'N_ID_CATEGORIA';  // Clave primaria

    public $incrementing = false;  // Si la clave primaria no es autoincrementable

    protected $fillable = ['N_ID_CATEGORIA', 'V_TITULO'];  // Campos que se pueden asignar masivamente
}
