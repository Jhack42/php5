<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BakiCategoria extends Model
{
    protected $table = 'baki_categorias';

    protected $primaryKey = 'N_ID_CATEGORIA';

    public $incrementing = false;  // Cambia a false solo si no es autoincremental

    public $timestamps = false;

    protected $fillable = [
        'N_ID_CATEGORIA',  // Inclúyelo solo si lo envías manualmente
        'V_TITULO',
    ];
}
