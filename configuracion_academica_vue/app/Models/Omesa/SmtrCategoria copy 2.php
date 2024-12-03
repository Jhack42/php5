<?php

namespace App\Models\Omesa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    // Si deseas controlar manualmente la asignación de N_ID_CATEGORIA a través de una secuencia
    public static function boot()
    {
        parent::boot();

        // Si no se proporciona un valor para N_ID_CATEGORIA, asignamos el siguiente valor de la secuencia
        static::creating(function ($model) {
            if (empty($model->N_ID_CATEGORIA)) {
                // Usar la secuencia de Oracle para obtener el siguiente valor para N_ID_CATEGORIA
                $model->N_ID_CATEGORIA = DB::select("SELECT OMESA.SEQ_SMTR_CATEGORIA.NEXTVAL AS NEXTVAL FROM DUAL")[0]->NEXTVAL;
            }
        });
    }
}
