<?php

namespace App\Models\Multimedia;  // Verifica que este namespace sea correcto


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Imagen extends Model
{
    protected $table = 'imagenes';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'imagen',
        'id_categoria_imagen'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaImagen::class, 'id_categoria_imagen', 'id_categoria_imagen');
    }

    public function carousels(): HasMany
    {
        return $this->hasMany(Carousel_designs::class, 'id_imagen', 'id');
    }
}
