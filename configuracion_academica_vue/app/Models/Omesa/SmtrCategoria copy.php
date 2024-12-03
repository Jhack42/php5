<?php

namespace App\Models\Omesa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmtrCategoria extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'OMESA.SMTR_CATEGORIA';

    // Llave primaria de la tabla
    protected $primaryKey = 'N_ID_CATEGORIA';

    // No usar timestamps automáticos
    public $timestamps = false;

    // Definir los campos que pueden ser asignados masivamente
    protected $fillable = ['V_TITULO'];
}
