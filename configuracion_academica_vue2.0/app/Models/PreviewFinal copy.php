<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreviewFinal extends Model
{
    protected $table = 'carousel_designs';

    protected $primaryKey = 'id_carousel_design';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'width_type',
        'width_value',
        'height_type',
        'height_value',
        'margin_top',
        'margin_bottom',
        'padding',
        'background_type',
        'background_color',
        'is_color_active',
        'background_image',
        'is_image_active',
        'background_video',
        'is_video_active',
        'overlay_color',
        'overlay_opacity',
        'border_radius',
        'box_shadow',
        'custom_css',
        'is_responsive',
        'breakpoints',
        'transition_effect',
        'transition_duration',
        'active'
    ];

    protected $casts = [
        'is_color_active' => 'boolean',
        'is_image_active' => 'boolean',
        'is_video_active' => 'boolean',
        'is_responsive' => 'boolean',
        'active' => 'boolean',
        'breakpoints' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'width_type' => 'responsive',
        'height_type' => 'fixed',
        'background_type' => 'color',
        'is_color_active' => 1,
        'is_image_active' => 0,
        'is_video_active' => 0,
        'is_responsive' => 1,
        'active' => 1
    ];

    // Método para actualizar el timestamp
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->created_at = $model->freshTimestamp();
        });

        static::updating(function ($model) {
            $model->updated_at = $model->freshTimestamp();
        });
    }

    // Método para obtener el diseño activo
    public static function getActiveDesign()
    {
        return static::where('active', 1)->first();
    }

    // Método para validar el tipo de ancho
    public function setWidthTypeAttribute($value)
    {
        $allowedTypes = ['responsive', 'fixed', 'fluid'];
        $this->attributes['width_type'] = in_array($value, $allowedTypes) ? $value : 'responsive';
    }

    // Método para validar el tipo de alto
    public function setHeightTypeAttribute($value)
    {
        $allowedTypes = ['fixed', 'aspect-ratio'];
        $this->attributes['height_type'] = in_array($value, $allowedTypes) ? $value : 'fixed';
    }

    // Método para validar el tipo de fondo
    public function setBackgroundTypeAttribute($value)
    {
        $allowedTypes = ['color', 'image', 'video'];
        $this->attributes['background_type'] = in_array($value, $allowedTypes) ? $value : 'color';
    }

    // Método para procesar los breakpoints antes de guardar
    public function setBreakpointsAttribute($value)
    {
        $this->attributes['breakpoints'] = is_array($value) ? json_encode($value) : $value;
    }

    // Método para obtener los breakpoints como array
    public function getBreakpointsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    // Método para generar el CSS completo
    public function generateCSS()
    {
        $css = $this->custom_css ?? '';

        // Agregar estilos base
        $css .= "
            .carousel-container {
                width: " . ($this->width_type === 'fixed' ? $this->width_value : '100%') . ";
                height: " . $this->height_value . ";
                margin-top: " . $this->margin_top . ";
                margin-bottom: " . $this->margin_bottom . ";
                padding: " . $this->padding . ";
                border-radius: " . $this->border_radius . ";
                box-shadow: " . $this->box_shadow . ";
                position: relative;
                overflow: hidden;
            }
        ";

        // Agregar estilos de fondo
        if ($this->background_type === 'color' && $this->is_color_active) {
            $css .= "
                .carousel-container {
                    background-color: " . $this->background_color . ";
                }
            ";
        }

        return $css;
    }
}
