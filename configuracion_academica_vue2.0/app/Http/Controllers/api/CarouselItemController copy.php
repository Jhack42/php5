<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CarouselItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarouselItemController extends Controller
{
    public function index()
    {
        try {
            $items = CarouselItem::all();
            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los items'
            ], 500);
        }
    }

    public function testNuevo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'html_content' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'display_order' => 'nullable|integer'
        ], [
            'name.required' => 'El campo nombre es requerido',
            'name.string' => 'El campo nombre debe ser texto',
            'name.max' => 'El nombre no debe exceder 200 caracteres'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();
            // Valores por defecto
            $data['is_active'] = $data['is_active'] ?? true;
            $data['display_order'] = $data['display_order'] ?? 0;

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

    public function editarSubCategoria(Request $request, $id)
    {
        try {
            $item = CarouselItem::findOrFail($id);
            $item->update($request->all());

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

    public function eliminar($id)
    {
        try {
            $item = CarouselItem::findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el item'
            ], 500);
        }
    }
}
