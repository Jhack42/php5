<?php

namespace App\Http\Controllers;

use App\Models\Categoria; // Importar el modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoriaController extends Controller
{
    public function index()
    {
        try {
            // Obtener todas las categorías
            $categorias = Categoria::all();

            // Retornar las categorías en un formato estándar
            return response()->json([
                'message' => 'Categorías obtenidas correctamente',
                'categorias' => $categorias,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener categorías: '.$e->getMessage());

            return response()->json(['message' => 'Error al obtener las categorías'], 500);
        }
    }

    // Crear una nueva categoría
    public function create(Request $request)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'v_titulo' => 'required|string|max:50',
            ]);

            // Crear la categoría
            $categoria = Categoria::create($request->only(['v_titulo']));

            return response()->json([
                'message' => 'Categoría creada correctamente',
                'categoria' => $categoria,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Error al crear categoría: '.$e->getMessage());

            return response()->json(['message' => 'Error al crear la categoría'], 500);
        }
    }

    // Editar una categoría existente
    public function update(Request $request, $id)
    {
        try {
            // Validar los datos de entrada
            $request->validate([
                'v_titulo' => 'required|string|max:50',
            ]);

            // Buscar la categoría por su ID
            $categoria = Categoria::find($id);

            if (! $categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            // Actualizar la categoría
            $categoria->update($request->only(['v_titulo']));

            return response()->json([
                'message' => 'Categoría actualizada correctamente',
                'categoria' => $categoria,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al actualizar categoría: '.$e->getMessage());

            return response()->json(['message' => 'Error al actualizar la categoría'], 500);
        }
    }

    // Eliminar una categoría
    public function delete($id)
    {
        try {
            // Buscar la categoría por su ID
            $categoria = Categoria::find($id);

            if (! $categoria) {
                return response()->json(['message' => 'Categoría no encontrada'], 404);
            }

            // Eliminar la categoría
            $categoria->delete();

            return response()->json(['message' => 'Categoría eliminada correctamente']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar categoría: '.$e->getMessage());

            return response()->json(['message' => 'Error al eliminar la categoría'], 500);
        }
    }

    // Buscar categorías por título
    public function search($titulo)
    {
        try {
            // Buscar categorías que coincidan con el título
            $categorias = Categoria::where('v_titulo', 'like', "%$titulo%")->get();

            if ($categorias->isEmpty()) {
                return response()->json(['message' => 'No se encontraron categorías'], 404);
            }

            return response()->json([
                'message' => 'Categorías encontradas',
                'categorias' => $categorias,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al buscar categorías: '.$e->getMessage());

            return response()->json(['message' => 'Error al buscar las categorías'], 500);
        }
    }
}
