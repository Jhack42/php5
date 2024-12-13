<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PreviewFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CarouselDesignTestController extends Controller
{
    /**
     * Listar todos los diseños
     */
    public function index()
    {
        try {
            $designs = PreviewFinal::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'message' => 'Diseños obtenidos correctamente',
                'data' => $designs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener diseños: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo diseño de prueba con datos predefinidos
     */
    public function testNuevo()
    {
        try {
            // Datos de prueba predefinidos
            $testData = [
                'name' => 'Diseño Test ' . date('Y-m-d H:i:s'),
                'width_type' => 'responsive',
                'width_value' => '100%',
                'height_type' => 'fixed',
                'height_value' => '400px',
                'margin_top' => '20px',
                'margin_bottom' => '20px',
                'padding' => '10px',
                'background_type' => 'color',
                'background_color' => '#' . substr(md5(mt_rand()), 0, 6),
                'is_color_active' => true,
                'border_radius' => '10px',
                'box_shadow' => '0 2px 4px rgba(0,0,0,0.1)',
                'custom_css' => '.test-class { color: #333; }',
                'is_responsive' => true,
                'transition_effect' => 'fade',
                'transition_duration' => '0.5s',
                'active' => true
            ];

            $design = PreviewFinal::create($testData);

            return response()->json([
                'success' => true,
                'message' => 'Diseño de prueba creado correctamente',
                'data' => $design,
                'generated_css' => $design->generateCSS()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear diseño de prueba: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Editar un diseño con datos de prueba
     */
    public function editarDiseño($id)
    {
        try {
            $design = PreviewFinal::findOrFail($id);

            // Datos de prueba para actualización
            $updateData = [
                'name' => 'Diseño Actualizado ' . date('Y-m-d H:i:s'),
                'background_type' => $design->background_type === 'color' ? 'image' : 'color',
                'background_color' => '#' . substr(md5(mt_rand()), 0, 6),
                'height_value' => rand(300, 600) . 'px',
                'is_responsive' => !$design->is_responsive,
                'custom_css' => '.updated-class { background: #fff; }'
            ];

            $design->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Diseño actualizado correctamente',
                'data' => $design,
                'updated_css' => $design->generateCSS()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar diseño: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un diseño
     */
    public function eliminar($id)
    {
        DB::beginTransaction();

        try {
            $design = PreviewFinal::findOrFail($id);

            // Guardar información antes de eliminar
            $deletedInfo = [
                'id' => $design->id_carousel_design,
                'name' => $design->name,
                'type' => $design->background_type
            ];

            // Eliminar archivos asociados si existen
            if ($design->background_image) {
                Storage::delete($design->background_image);
            }
            if ($design->background_video) {
                Storage::delete($design->background_video);
            }

            $design->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Diseño eliminado correctamente',
                'deleted_data' => array_merge($deletedInfo, ['deleted_at' => now()])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el diseño',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear múltiples diseños de prueba
     */
    public function crearVariosTest($cantidad = 3)
    {
        try {
            $designs = [];
            $types = ['color', 'image', 'video'];
            $widthTypes = ['responsive', 'fixed', 'fluid'];

            for ($i = 0; $i < $cantidad; $i++) {
                $testData = [
                    'name' => 'Diseño Test ' . ($i + 1),
                    'width_type' => $widthTypes[array_rand($widthTypes)],
                    'width_value' => $widthTypes[array_rand($widthTypes)] === 'fixed' ?
                        rand(800, 1200) . 'px' : '100%',
                    'height_type' => 'fixed',
                    'height_value' => rand(300, 600) . 'px',
                    'background_type' => $types[array_rand($types)],
                    'background_color' => '#' . substr(md5(mt_rand()), 0, 6),
                    'is_color_active' => true,
                    'border_radius' => rand(0, 20) . 'px',
                    'is_responsive' => rand(0, 1) == 1,
                    'active' => $i === 0 // Solo el primero activo
                ];

                $designs[] = PreviewFinal::create($testData);
            }

            return response()->json([
                'success' => true,
                'message' => "Se crearon $cantidad diseños de prueba",
                'data' => $designs
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear diseños de prueba: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test de generación de CSS
     */
    public function testGenerarCSS($id)
    {
        try {
            $design = PreviewFinal::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'CSS generado correctamente',
                'data' => [
                    'design' => $design,
                    'css' => $design->generateCSS(),
                    'breakpoints' => $design->breakpoints
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar CSS: ' . $e->getMessage()
            ], 500);
        }
    }
}
