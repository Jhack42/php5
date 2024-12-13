<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CarouselItemController extends Controller
{
    /**
     * Obtener listado de items del carousel
     */
    public function index(Request $request)
    {
        try {
            $query = CarouselItem::query();

            // Filtrar por estado activo si se especifica
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            // Ordenar por orden de visualización
            $items = $query->orderBy('display_order')->get();

            return response()->json([
                'success' => true,
                'message' => 'Items obtenidos correctamente',
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener items'
            ], 500);
        }
    }

    /**
     * Crear nuevo item del carousel
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'html_content' => 'nullable|string',
            'mobile_html_content' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
            'category' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transition_effect' => 'nullable|string|max:50',
            'background_color' => 'nullable|string|max:20',
            'custom_styles' => 'nullable|string',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $validator->validated();

            // Agregar datos de auditoría
            $data['created_by'] = $request->user() ? $request->user()->name : 'system';
            $data['updated_by'] = $data['created_by'];

            $item = CarouselItem::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Item creado correctamente',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el item'
            ], 500);
        }
    }

    /**
     * Actualizar un item del carousel
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:200',
            'description' => 'nullable|string',
            'html_content' => 'nullable|string',
            'mobile_html_content' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer',
            'category' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transition_effect' => 'nullable|string|max:50',
            'background_color' => 'nullable|string|max:20',
            'custom_styles' => 'nullable|string',
            'metadata' => 'nullable|json'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item = CarouselItem::findOrFail($id);

            $data = $validator->validated();
            $data['updated_by'] = $request->user() ? $request->user()->name : 'system';

            $item->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Item actualizado correctamente',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el item'
            ], 500);
        }
    }

    /**
     * Eliminar un item del carousel
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $item = CarouselItem::findOrFail($id);

            // Desactivar trigger de historial
            DB::statement("ALTER TRIGGER trg_carousel_items_history DISABLE");

            // Eliminar registros relacionados
            DB::statement("DELETE FROM CAROUSEL_ITEMS_HISTORY WHERE ITEM_ID = :id", ['id' => $id]);
            DB::statement("DELETE FROM CAROUSEL_ITEMS WHERE ID = :id", ['id' => $id]);

            // Reactivar trigger
            DB::statement("ALTER TRIGGER trg_carousel_items_history ENABLE");

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente',
                'data' => [
                    'id' => $id,
                    'name' => $item->name
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            try {
                DB::statement("ALTER TRIGGER trg_carousel_items_history ENABLE");
            } catch (\Exception $triggerEx) {}

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el item'
            ], 500);
        }
    }
}
