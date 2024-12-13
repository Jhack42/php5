<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CarouselItem extends Model
{
    protected $table = 'php5.carousel_items';  // Especifica el esquema php5 en minúsculas
    protected $primaryKey = 'id'; // Clave primaria en minúsculas
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'html_content',
        'display_order',
        'is_active',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'category',
        'transition_effect',
        'background_color',
        'custom_styles',
        'mobile_html_content',
        'metadata'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'display_order' => 'integer',
        'metadata' => 'array'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'ASC');
    }
}
