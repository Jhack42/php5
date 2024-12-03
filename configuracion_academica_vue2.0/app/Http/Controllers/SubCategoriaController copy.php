<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubCategoriaController extends Controller
{
    // Crear una nueva subcategoría
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'n_id_categoria' => 'required|exists:smtr_categoria,n_id_categoria',
                'v_descripcion' => 'required|string|max:150',
            ]);

            // Crear la subcategoría
            $subCategoria = SubCategoria::create($validatedData);

            return response()->json([
                'message' => 'Subcategoría creada exitosamente',
                'data' => $subCategoria,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear subcategoría: '.$e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al crear la subcategoría',
            ], 500);
        }
    }

    // Obtener una subcategoría por su ID
    public function show($id)
    {
        $subCategoria = SubCategoria::find($id);

        if ($subCategoria) {
            return response()->json([
                'message' => 'Subcategoría encontrada',
                'data' => $subCategoria,
            ]);
        }

        return response()->json([
            'message' => 'Subcategoría no encontrada',
        ], 404);
    }

    // Actualizar una subcategoría existente
    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $validatedData = $request->validate([
                'n_id_categoria' => 'sometimes|exists:smtr_categoria,n_id_categoria',
                'v_descripcion' => 'sometimes|string|max:150',
            ]);

            $subCategoria = SubCategoria::find($id);

            if ($subCategoria) {
                $subCategoria->update($validatedData);

                return response()->json([
                    'message' => 'Subcategoría actualizada exitosamente',
                    'data' => $subCategoria,
                ]);
            }

            return response()->json([
                'message' => 'Subcategoría no encontrada',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error al actualizar subcategoría: '.$e->getMessage());

            return response()->json([
                'message' => 'Ocurrió un error al actualizar la subcategoría',
            ], 500);
        }
    }

    // Eliminar una subcategoría
    public function destroy($id)
    {
        $subCategoria = SubCategoria::find($id);

        if ($subCategoria) {
            $subCategoria->delete();

            return response()->json([
                'message' => 'Subcategoría eliminada exitosamente',
            ]);
        }

        return response()->json([
            'message' => 'Subcategoría no encontrada',
        ], 404);
    }

    // Listar todas las subcategorías o filtrar por descripción
    public function index(Request $request)
    {
        $query = SubCategoria::query();

        if ($request->has('v_descripcion')) {
            $query->where('v_descripcion', 'like', '%'.$request->v_descripcion.'%');
        }

        $subCategorias = $query->get();

        return response()->json([
            'message' => 'Subcategorías obtenidas exitosamente',
            'data' => $subCategorias,
        ]);
    }
}
