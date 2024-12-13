<?php

namespace App\Models\Multimedia;  // Verifica que este namespace sea correcto


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    protected $table = 'videos';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'descripcion',
        'video',
        'id_categoria_video'
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaVideo::class, 'id_categoria_video', 'id_categoria_video');
    }

    public function carousels(): HasMany
    {
        return $this->hasMany(Carousel_designs::class, 'id_video', 'id');
    }
}
