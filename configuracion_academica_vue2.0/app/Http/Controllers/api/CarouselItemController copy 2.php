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
        // Iniciamos una transacción
        DB::beginTransaction();

        try {
            // Verificamos si el item existe
            $item = CarouselItem::findOrFail($id);

            // Verificamos si hay registros relacionados
            $hasRelatedRecords = DB::select("
                SELECT table_name, constraint_name
                FROM all_constraints
                WHERE r_constraint_name IN (
                    SELECT constraint_name
                    FROM all_constraints
                    WHERE table_name = 'CAROUSEL_ITEMS'
                    AND constraint_type = 'P'
                )
                AND constraint_type = 'R'
            ");

            if (!empty($hasRelatedRecords)) {
                // Si hay registros relacionados, desactivamos el item en lugar de eliminarlo
                $item->update([
                    'is_active' => false,
                    'updated_by' => 'system_delete'
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'El item tiene registros relacionados. Se ha desactivado en lugar de eliminarse.',
                    'data' => $item,
                    'action_taken' => 'deactivated'
                ]);
            }

            // Si no hay registros relacionados, procedemos con la eliminación
            $itemData = $item->toArray(); // Guardamos la información antes de eliminar
            $item->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente',
                'data' => $itemData,
                'action_taken' => 'deleted'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'El item no existe',
                'error_code' => 'ITEM_NOT_FOUND'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            // Intentamos un soft delete si el error es de restricción de integridad
            if (stripos($e->getMessage(), 'ORA-02292') !== false) {
                try {
                    $item->update([
                        'is_active' => false,
                        'updated_by' => 'system_delete'
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'No se pudo eliminar el item debido a registros relacionados. Se ha desactivado en su lugar.',
                        'data' => $item,
                        'action_taken' => 'deactivated'
                    ]);

                } catch (\Exception $innerException) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No se pudo eliminar ni desactivar el item',
                        'error_details' => $innerException->getMessage(),
                        'error_code' => 'OPERATION_FAILED'
                    ], 500);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error_details' => $e->getMessage(),
                'error_code' => 'UNKNOWN_ERROR'
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
