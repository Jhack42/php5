<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    protected $table = 'carousel_items';

    protected $primaryKey = 'id';

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
        'view_count',
        'transition_effect',
        'background_color',
        'custom_styles',
        'mobile_html_content',
        'metadata'
    ];

    // Casteos de tipos de datos
    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'view_count' => 'integer'
    ];
}
