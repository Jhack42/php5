<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facultad extends Model
{
    // Indicar el nombre de la tabla en la base de datos (en caso de que no siga la convención plural).
    protected $table = 'facultad';

    // Indicar el nombre de la clave primaria de la tabla.
    protected $primaryKey = 'id_facultad';

    // Si la clave primaria no es autoincremental, indica que no es un campo de incremento automático.
    public $incrementing = false;

    // Definir los tipos de datos para los campos de la tabla, si es necesario.
    protected $casts = [
        'id_facultad' => 'integer',
        'nombre' => 'string',
    ];

    // Indicar los campos que se pueden asignar en masa (para evitar vulnerabilidades de asignación masiva).
    protected $fillable = ['id_facultad', 'nombre'];

    // Desactivar la gestión automática de las fechas 'created_at' y 'updated_at' si no las tienes en la tabla.
    public $timestamps = false;
}
