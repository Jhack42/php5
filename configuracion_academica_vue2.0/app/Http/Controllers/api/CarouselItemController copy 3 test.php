<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarouselItemController extends Controller
{
    /**
     * Listar todos los items
     */
    public function index()
    {
        try {
            $items = CarouselItem::orderBy('display_order')->get();
            return response()->json([
                'success' => true,
                'message' => 'Items obtenidos correctamente',
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo item de prueba con datos predefinidos
     */
    public function testNuevo()
    {
        try {
            // Datos de prueba predefinidos
            $testData = [
                'name' => 'Carousel Test ' . date('Y-m-d H:i:s'),
                'description' => 'Esta es una descripción de prueba',
                'html_content' => '<div class="test">Contenido HTML de prueba</div>',
                'is_active' => true,
                'display_order' => rand(1, 100),
                'category' => 'test',
                'background_color' => '#' . substr(md5(mt_rand()), 0, 6),
                'created_by' => 'test_user',
                'updated_by' => 'test_user'
            ];

            $item = CarouselItem::create($testData);

            return response()->json([
                'success' => true,
                'message' => 'Item de prueba creado correctamente',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear item de prueba: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Editar un item con datos de prueba
     */
    public function editarSubCategoria($id)
    {
        try {
            $item = CarouselItem::findOrFail($id);

            // Datos de prueba para actualización
            $updateData = [
                'name' => 'Carousel Actualizado ' . date('Y-m-d H:i:s'),
                'description' => 'Descripción actualizada de prueba',
                'is_active' => !$item->is_active, // Cambia el estado actual
                'display_order' => rand(1, 100),
                'updated_by' => 'test_user_update'
            ];

            $item->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Item actualizado correctamente',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un item
     */
    public function eliminar($id)
    {
        DB::beginTransaction();

        try {
            // 1. Verificamos si el registro existe
            $item = CarouselItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado'
                ], 404);
            }

            // 2. Desactivamos temporalmente el trigger de historial
            DB::statement("ALTER TRIGGER trg_carousel_items_history DISABLE");

            // 3. Eliminamos los registros de historial
            DB::statement("DELETE FROM CAROUSEL_ITEMS_HISTORY WHERE ITEM_ID = :id", ['id' => $id]);

            // 4. Eliminamos el registro principal
            DB::statement("DELETE FROM CAROUSEL_ITEMS WHERE ID = :id", ['id' => $id]);

            // 5. Reactivamos el trigger
            DB::statement("ALTER TRIGGER trg_carousel_items_history ENABLE");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item y su historial han sido eliminados correctamente',
                'deleted_data' => [
                    'id' => $id,
                    'name' => $item->name,
                    'deleted_at' => now()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Aseguramos que el trigger quede habilitado incluso si hay error
            try {
                DB::statement("ALTER TRIGGER trg_carousel_items_history ENABLE");
            } catch (\Exception $triggerEx) {
                // Si hay error al reactivar el trigger, lo agregamos al mensaje
                return response()->json([
                    'success' => false,
                    'message' => 'Error crítico: El trigger puede haber quedado deshabilitado',
                    'error' => $e->getMessage(),
                    'trigger_error' => $triggerEx->getMessage()
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear múltiples items de prueba
     */
    public function crearVariosTest($cantidad = 5)
    {
        try {
            $items = [];
            for ($i = 0; $i < $cantidad; $i++) {
                $testData = [
                    'name' => 'Carousel Test ' . ($i + 1),
                    'description' => 'Descripción de prueba ' . ($i + 1),
                    'html_content' => '<div class="test">Contenido HTML ' . ($i + 1) . '</div>',
                    'is_active' => rand(0, 1) == 1,
                    'display_order' => $i + 1,
                    'category' => 'test_category_' . rand(1, 3),
                    'background_color' => '#' . substr(md5(mt_rand()), 0, 6),
                    'created_by' => 'test_user',
                    'updated_by' => 'test_user'
                ];

                $items[] = CarouselItem::create($testData);
            }

            return response()->json([
                'success' => true,
                'message' => "Se crearon $cantidad items de prueba",
                'data' => $items
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear items de prueba: ' . $e->getMessage()
            ], 500);
        }
    }
}
