<?php

namespace App\Http\Controllers;

use App\Models\CarouselItem;
use App\Events\CarouselItemUpdated;
use App\Events\CarouselItemDeleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CarouselController extends Controller
{
    /**
     * Obtener todos los items del carrusel
     */
    public function index(Request $request)
    {
        try {
            $query = CarouselItem::query();

            // Filtrar por estado activo si se especifica
            if ($request->has('active')) {
                $query->where('IS_ACTIVE', $request->boolean('active'));
            }

            // Filtrar por categoría
            if ($request->has('category')) {
                $query->where('CATEGORY', $request->category);
            }

            // Obtener solo items visibles actualmente
            if ($request->boolean('visible')) {
                $query->currentlyVisible();
            }

            $items = $query->ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener items del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener items del carrusel'
            ], 500);
        }
    }

    /**
     * Crear un nuevo item
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'NAME' => 'required|string|max:200',
                'DESCRIPTION' => 'nullable|string',
                'HTML_CONTENT' => 'required|string',
                'DISPLAY_ORDER' => 'nullable|integer',
                'START_DATE' => 'nullable|date',
                'END_DATE' => 'nullable|date|after_or_equal:START_DATE',
                'CATEGORY' => 'nullable|string|max:50',
                'TRANSITION_EFFECT' => 'nullable|string|max:50',
                'BACKGROUND_COLOR' => 'nullable|string|max:20',
                'CUSTOM_STYLES' => 'nullable|string',
                'MOBILE_HTML_CONTENT' => 'nullable|string',
                'METADATA' => 'nullable|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['CREATED_BY'] = Auth::id() ?? 'system';
            $data['UPDATED_BY'] = Auth::id() ?? 'system';

            $item = CarouselItem::create($data);

            // Emitir evento WebSocket
            broadcast(new CarouselItemUpdated($item))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear item del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear item del carrusel'
            ], 500);
        }
    }

    /**
     * Obtener un item específico
     */
    public function show($id)
    {
        try {
            $item = CarouselItem::findOrFail($id);
            $item->incrementViewCount();

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener item del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Item no encontrado'
            ], 404);
        }
    }

    /**
     * Actualizar un item
     */
    public function update(Request $request, $id)
    {
        try {
            $item = CarouselItem::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'NAME' => 'sometimes|required|string|max:200',
                'DESCRIPTION' => 'nullable|string',
                'HTML_CONTENT' => 'sometimes|required|string',
                'DISPLAY_ORDER' => 'nullable|integer',
                'IS_ACTIVE' => 'nullable|boolean',
                'START_DATE' => 'nullable|date',
                'END_DATE' => 'nullable|date|after_or_equal:START_DATE',
                'CATEGORY' => 'nullable|string|max:50',
                'TRANSITION_EFFECT' => 'nullable|string|max:50',
                'BACKGROUND_COLOR' => 'nullable|string|max:20',
                'CUSTOM_STYLES' => 'nullable|string',
                'MOBILE_HTML_CONTENT' => 'nullable|string',
                'METADATA' => 'nullable|json'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = $validator->validated();
            $data['UPDATED_BY'] = Auth::id() ?? 'system';

            $item->update($data);

            // Emitir evento WebSocket
            broadcast(new CarouselItemUpdated($item))->toOthers();

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar item del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar item del carrusel'
            ], 500);
        }
    }

    /**
     * Eliminar un item
     */
    public function destroy($id)
    {
        try {
            $item = CarouselItem::findOrFail($id);
            $item->delete();

            // Emitir evento WebSocket
            broadcast(new CarouselItemDeleted($id))->toOthers();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar item del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar item del carrusel'
            ], 500);
        }
    }

    /**
     * Reordenar items
     */
    public function reorder(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'items' => 'required|array',
                'items.*' => 'required|integer|exists:CAROUSEL_ITEMS,ID'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Actualizar el orden de los items
            foreach ($request->items as $index => $id) {
                CarouselItem::where('ID', $id)->update([
                    'DISPLAY_ORDER' => $index,
                    'UPDATED_BY' => Auth::id() ?? 'system'
                ]);
            }

            $updatedItems = CarouselItem::whereIn('ID', $request->items)
                ->ordered()
                ->get();

            // Emitir evento WebSocket para cada item actualizado
            foreach ($updatedItems as $item) {
                broadcast(new CarouselItemUpdated($item))->toOthers();
            }

            return response()->json([
                'success' => true,
                'data' => $updatedItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error al reordenar items del carrusel: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar items'
            ], 500);
        }
    }
}
