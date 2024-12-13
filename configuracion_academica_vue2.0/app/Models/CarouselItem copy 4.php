<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItem extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'carousel_items';

    /**
     * The schema associated with the model.
     *
     * @var string
     */
    protected $connection = 'oracle';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'display_order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => 'json',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created_at';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated_at';
}
