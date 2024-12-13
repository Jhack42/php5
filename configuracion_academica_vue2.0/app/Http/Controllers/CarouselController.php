<?php

namespace App\Http\Controllers;

use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CarouselController extends Controller
{
    /**
     * Listar todos los items
     */
    public function index(Request $request)
    {
        try {
            $query = CarouselItem::query();

            if ($request->has('active')) {
                $query->where('IS_ACTIVE', $request->boolean('active'));
            }

            if ($request->has('category')) {
                $query->where('CATEGORY', $request->category);
            }

            $items = $query->ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener items',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un item específico
     */
    public function show($id)
    {
        try {
            $item = CarouselItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo item
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'NAME' => 'required|string|max:200',
                'DESCRIPTION' => 'nullable|string',
                'HTML_CONTENT' => 'required|string',
                'DISPLAY_ORDER' => 'nullable|integer',
                'IS_ACTIVE' => 'nullable|boolean',
                'START_DATE' => 'nullable|date',
                'END_DATE' => 'nullable|date|after_or_equal:START_DATE',
            ]);

            $item = CarouselItem::create($data);

            return response()->json([
                'success' => true,
                'data' => $item
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar item
     */
    public function update(Request $request, $id)
    {
        try {
            $item = CarouselItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado'
                ], 404);
            }

            $data = $request->validate([
                'NAME' => 'sometimes|required|string|max:200',
                'DESCRIPTION' => 'nullable|string',
                'HTML_CONTENT' => 'sometimes|required|string',
                'DISPLAY_ORDER' => 'nullable|integer',
                'IS_ACTIVE' => 'nullable|boolean',
                'START_DATE' => 'nullable|date',
                'END_DATE' => 'nullable|date|after_or_equal:START_DATE',
            ]);

            $item->update($data);

            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar item
     */
    public function destroy($id)
    {
        try {
            $item = CarouselItem::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item no encontrado'
                ], 404);
            }

            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al eliminar item: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar item',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reordenar items
     */
    public function reorder(Request $request)
    {
        try {
            $data = $request->validate([
                'items' => 'required|array',
                'items.*' => 'required|integer|exists:PHP5.CAROUSEL_ITEMS,ID'
            ]);

            foreach ($data['items'] as $index => $id) {
                CarouselItem::where('ID', $id)
                    ->update(['DISPLAY_ORDER' => $index]);
            }

            $updatedItems = CarouselItem::whereIn('ID', $data['items'])
                ->ordered()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Items reordenados correctamente',
                'data' => $updatedItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error al reordenar items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al reordenar items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
