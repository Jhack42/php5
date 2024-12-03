<?php

namespace App\Models\Omesa;

use Illuminate\Database\Eloquent\Model;

class SmtrCategoria extends Model
{
    // Establecemos la tabla y la clave primaria
    protected $table = 'SMTR_CATEGORIA';
    protected $primaryKey = 'N_ID_CATEGORIA';

    // Los campos que se pueden asignar masivamente
    protected $fillable = ['V_TITULO'];

    // Desactivar la auto-incrementación de 'N_ID_CATEGORIA' ya que es manejado por el trigger
    public $incrementing = false;  // Evita que Laravel intente usar auto incremental.

    // Desactivar la necesidad de timestamps si no los usas en esta tabla
    public $timestamps = false;

    // No necesitamos definir una secuencia ya que el trigger maneja el ID automáticamente
}
