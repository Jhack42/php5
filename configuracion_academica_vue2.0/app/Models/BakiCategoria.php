<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BakiCategoria extends Model
{
    // Nombre de la tabla en la base de datos
    protected $table = 'BAKI_CATEGORIAS';

    // Definir la columna de la clave primaria (si no es 'id')
    protected $primaryKey = 'N_ID_CATEGORIA';

    // Asegurarse de que Laravel no intente manejar la auto-incrementación
    public $incrementing = false;

    // Si estamos usando una secuencia (como en Oracle) no necesitamos timestamps automáticos, así que desactivamos
    public $timestamps = false;

    // Los campos que se pueden asignar de forma masiva (mass assignable)
    protected $fillable = ['V_TITULO'];
}
