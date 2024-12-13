<?php

namespace App\Models\Multimedia;  // Verifica que este namespace sea correcto


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaImagen extends Model
{
    protected $table = 'categoria_imagen';
    protected $primaryKey = 'id_categoria_imagen';
    public $timestamps = true;

    protected $fillable = [
        'categoria'
    ];

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'id_categoria_imagen', 'id_categoria_imagen');
    }
}
