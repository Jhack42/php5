<?php
// app/Models/CarouselItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CarouselItem extends Model
{
    // Nombre de la tabla en Oracle
    protected $table = 'CAROUSEL_ITEMS';

    // Clave primaria
    protected $primaryKey = 'ID';

    // Desactivar timestamps de Laravel ya que usamos CREATED_AT y UPDATED_AT personalizados
    public $timestamps = false;

    // Campos que se pueden llenar masivamente
    protected $fillable = [
        'NAME',
        'DESCRIPTION',
        'HTML_CONTENT',
        'DISPLAY_ORDER',
        'IS_ACTIVE',
        'START_DATE',
        'END_DATE',
        'CREATED_BY',
        'UPDATED_BY',
        'CATEGORY',
        'TRANSITION_EFFECT',
        'BACKGROUND_COLOR',
        'CUSTOM_STYLES',
        'MOBILE_HTML_CONTENT',
        'METADATA'
    ];

    // Conversión de tipos
    protected $casts = [
        'CREATED_AT' => 'datetime',
        'UPDATED_AT' => 'datetime',
        'START_DATE' => 'datetime',
        'END_DATE' => 'datetime',
        'IS_ACTIVE' => 'boolean',
        'VIEW_COUNT' => 'integer',
        'DISPLAY_ORDER' => 'integer',
        'METADATA' => 'array'
    ];

    // Atributos por defecto
    protected $attributes = [
        'IS_ACTIVE' => 1,
        'DISPLAY_ORDER' => 0,
        'VIEW_COUNT' => 0
    ];

    // Relación con el historial
    public function history()
    {
        return $this->hasMany(CarouselItemHistory::class, 'ITEM_ID', 'ID');
    }

    // Scopes para consultas comunes
    public function scopeActive($query)
    {
        return $query->where('IS_ACTIVE', 1);
    }

    public function scopeCurrentlyVisible($query)
    {
        return $query->where('IS_ACTIVE', 1)
            ->where(function($q) {
                $now = now();
                $q->whereNull('START_DATE')
                  ->orWhere('START_DATE', '<=', $now);
            })
            ->where(function($q) {
                $now = now();
                $q->whereNull('END_DATE')
                  ->orWhere('END_DATE', '>=', $now);
            });
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('DISPLAY_ORDER', 'ASC');
    }

    // Métodos para el WebSocket
    public static function boot()
    {
        parent::boot();

        // Evento después de cualquier cambio para notificar via WebSocket
        static::saved(function ($model) {
            broadcast(new CarouselItemUpdated($model));
        });

        static::deleted(function ($model) {
            broadcast(new CarouselItemDeleted($model));
        });
    }

    // Incrementar el contador de vistas
    public function incrementViewCount()
    {
        $this->increment('VIEW_COUNT');
    }
}

// app/Models/CarouselItemHistory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarouselItemHistory extends Model
{
    protected $table = 'CAROUSEL_ITEMS_HISTORY';

    protected $primaryKey = 'HISTORY_ID';

    public $timestamps = false;

    protected $fillable = [
        'ITEM_ID',
        'CHANGE_TYPE',
        'CHANGED_BY',
        'OLD_VALUES',
        'NEW_VALUES'
    ];

    protected $casts = [
        'CHANGED_AT' => 'datetime',
        'OLD_VALUES' => 'array',
        'NEW_VALUES' => 'array'
    ];

    // Relación con el item del carrusel
    public function carouselItem()
    {
        return $this->belongsTo(CarouselItem::class, 'ITEM_ID', 'ID');
    }
}
