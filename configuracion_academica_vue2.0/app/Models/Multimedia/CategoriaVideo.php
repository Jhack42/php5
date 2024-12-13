<?php

namespace App\Models\Multimedia;  // Verifica que este namespace sea correcto


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaVideo extends Model
{
    protected $table = 'categoria_video';
    protected $primaryKey = 'id_categoria_video';
    public $timestamps = true;

    protected $fillable = [
        'categoria'
    ];

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class, 'id_categoria_video', 'id_categoria_video');
    }
}
