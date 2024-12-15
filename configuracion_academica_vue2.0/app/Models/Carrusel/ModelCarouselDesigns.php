<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModelCarouselDesigns extends Model
{
    protected $table = 'carousel_designs';
    protected $primaryKey = 'id_carousel_design';

    // Activamos timestamps ya que tenemos created_at y updated_at
    public $timestamps = true;

    protected $fillable = [
        'name',
        'id_imagen',
        'id_video',
        'custom_css'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con la tabla de imágenes
    public function imagen(): BelongsTo
    {
        return $this->belongsTo(Imagen::class, 'id_imagen', 'id');
    }

    // Relación con la tabla de videos
    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class, 'id_video', 'id');
    }

    // Método para generar el CSS
    public function generateCSS(): string
    {
        return $this->custom_css ?? '';
    }

    // Método para obtener el carrusel con sus relaciones
    public static function getCarouselWithRelations(int $id)
    {
        return static::with(['imagen', 'video'])
            ->findOrFail($id);
    }

    // Método para crear un nuevo carrusel
    public static function createCarousel(array $data)
    {
        return static::create([
            'name' => $data['name'],
            'id_imagen' => $data['id_imagen'] ?? null,
            'id_video' => $data['id_video'] ?? null,
            'custom_css' => $data['custom_css'] ?? null
        ]);
    }

    // Método para actualizar un carrusel existente
    public function updateCarousel(array $data): bool
    {
        return $this->update([
            'name' => $data['name'] ?? $this->name,
            'id_imagen' => $data['id_imagen'] ?? $this->id_imagen,
            'id_video' => $data['id_video'] ?? $this->id_video,
            'custom_css' => $data['custom_css'] ?? $this->custom_css
        ]);
    }
}