<?php

// ControllerImagen.php
namespace App\Http\Controllers\Multimedia;

use App\Http\Controllers\Controller;
use App\Models\Multimedia\Imagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ControllerImagen extends Controller
{
    public function index()
    {
        try {
            $imagenes = Imagen::with('categoria')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Imágenes obtenidas correctamente',
                'data' => $imagenes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener imágenes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:200',
                'imagen' => 'required|string',
                'id_categoria_imagen' => 'required|exists:categoria_imagen,id_categoria_imagen'
            ]);

            $imagen = Imagen::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Imagen creada correctamente',
                'data' => $imagen
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:200',
                'imagen' => 'sometimes|required|string',
                'id_categoria_imagen' => 'sometimes|required|exists:categoria_imagen,id_categoria_imagen'
            ]);

            $imagen = Imagen::findOrFail($id);
            $imagen->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Imagen actualizada correctamente',
                'data' => $imagen
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $imagen = Imagen::findOrFail($id);

            if ($imagen->carousels()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la imagen porque está siendo utilizada en carruseles'
                ], 400);
            }

            $imagen->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar imagen: ' . $e->getMessage()
            ], 500);
        }
    }
}
