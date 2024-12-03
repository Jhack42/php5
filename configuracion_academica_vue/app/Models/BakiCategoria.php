<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BakiCategoria extends Model
{
    // Especificamos el nombre de la tabla (en caso de que no sea plural)
    protected $table = 'baki_categorias';

    // Especificamos la clave primaria si es diferente de 'id'
    protected $primaryKey = 'N_ID_CATEGORIA';

    // Indicamos que la clave primaria es un número (no un incremento automático)
    public $incrementing = false;  // Si usas un tipo de dato diferente, como NUMBER

    // Definir los campos que se pueden asignar masivamente
    protected $fillable = [
        'N_ID_CATEGORIA',
        'V_TITULO',
    ];

    // Si no quieres que se maneje la fecha de creación/actualización automáticamente
    public $timestamps = false; // Establece en false si no estás usando created_at y updated_at
}
