<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class CarouselItemController extends Controller
{
    /**
     * Obtener listado de items del carousel
     */
    public function index(Request $request)
    {
        try {
            $query = CarouselItem::query();

            // Filtros
            if ($request->has('active')) {
                $query->where('is_active', $request->boolean('active'));
            }

            if ($request->has('category')) {
                $query->where('category', $request->category);
            }

            if ($request->has('date')) {
                $date = Carbon::parse($request->date);
                $query->where(function($q) use ($date) {
                    $q->whereNull('start_date')
                      ->orWhere('start_date', '<=', $date);
                })->where(function($q) use ($date) {
                    $q->whereNull('end_date')
                      ->orWhere('end_date', '>=', $date);
                });
            }

            // Ordenamiento
            $orderBy = $request->get('order_by', 'display_order');
            $orderDir = $request->get('order_dir', 'asc');
            $items = $query->orderBy($orderBy, $orderDir)->get();

            return response()->json([
                'success' => true,
                'message' => 'Items obtenidos correctamente',
                'data' => $items,
                'total' => $items->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener items',
                'error' => $e->getMessage()
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
            'display_order' => 'integer|min:0',
            'category' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'transition_effect' => 'nullable|string|max:50',
            'background_color' => 'nullable|string|max:20',
            'custom_styles' => 'nullable|string',
            'metadata' => 'nullable|json'
        ], [
            'name.required' => 'El nombre es requerido',
            'name.max' => 'El nombre no puede exceder los 200 caracteres',
            'display_order.min' => 'El orden debe ser un número positivo',
            'end_date.after_or_equal' => 'La fecha de fin debe ser posterior o igual a la fecha de inicio'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();

            // Formato de fechas
            if (!empty($data['start_date'])) {
                $data['start_date'] = Carbon::parse($data['start_date'])->format('Y-m-d H:i:s');
            }
            if (!empty($data['end_date'])) {
                $data['end_date'] = Carbon::parse($data['end_date'])->format('Y-m-d H:i:s');
            }

            // Datos de auditoría
            $data['created_by'] = $request->user() ? $request->user()->name : 'system';
            $data['updated_by'] = $data['created_by'];

            $item = CarouselItem::create($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item creado correctamente',
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar un item del carousel
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:200',
            'description' => 'nullable|string',
            'html_content' => 'nullable|string',
            'mobile_html_content' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'integer|min:0',
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
            DB::beginTransaction();

            $item = CarouselItem::findOrFail($id);

            $data = $validator->validated();

            // Formato de fechas
            if (isset($data['start_date'])) {
                $data['start_date'] = $data['start_date'] ? Carbon::parse($data['start_date'])->format('Y-m-d H:i:s') : null;
            }
            if (isset($data['end_date'])) {
                $data['end_date'] = $data['end_date'] ? Carbon::parse($data['end_date'])->format('Y-m-d H:i:s') : null;
            }

            $data['updated_by'] = $request->user() ? $request->user()->name : 'system';

            $item->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Item actualizado correctamente',
                'data' => $item
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el item',
                'error' => $e->getMessage()
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
                'message' => 'Error al eliminar el item',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
