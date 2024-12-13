<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    protected $table = 'carousel_items';  // Nombre de la tabla en Oracle

    protected $primaryKey = 'id'; // Clave primaria

    public $timestamps = false; // Desactiva los timestamps automáticos

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
        'view_count',
        'transition_effect',
        'background_color',
        'custom_styles',
        'mobile_html_content',
        'metadata'
    ];
}
