<?php

namespace App\Http\Controllers\Omesa;

use App\Http\Controllers\Controller;
use App\Models\Omesa\SmtrCategoria;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSmtrCategoriaRequest;
use App\Http\Requests\UpdateSmtrCategoriaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SmtrCategoriaController extends Controller
{
    /**
     * Mostrar todas las categorías.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $categorias = SmtrCategoria::all();

            return response()->json([
                'data' => $categorias,
                'message' => 'Categorías obtenidas con éxito.',
            ], 200);
        } catch (\Exception $e) {
            // Manejo de errores general para obtener las categorías
            return response()->json([
                'error' => 'Error al obtener las categorías.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Almacenar una nueva categoría.
     *
     * @param StoreSmtrCategoriaRequest $request
     * @return JsonResponse
     */
    public function store(StoreSmtrCategoriaRequest $request): JsonResponse
{
    $data = $request->validated();

    try {
        $categoria = SmtrCategoria::create($data);  // Creamos la categoría

        return response()->json([
            'message' => 'Categoría creada exitosamente.',
            'data' => $categoria
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al crear la categoría.',
            'message' => $e->getMessage(),
        ], 500);
    }
}

public function update(StoreSmtrCategoriaRequest $request, $id): JsonResponse
{
    try {
        $categoria = SmtrCategoria::findOrFail($id);  // Encontramos la categoría por ID
        $categoria->update($request->validated());  // Actualizamos la categoría

        return response()->json([
            'data' => $categoria,
            'message' => 'Categoría actualizada con éxito.',
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'error' => 'Categoría no encontrada.',
            'message' => 'No se pudo encontrar la categoría con el ID proporcionado.',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al actualizar la categoría.',
            'message' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Eliminar la categoría especificada.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            // Buscar la categoría por ID
            $categoria = SmtrCategoria::findOrFail($id);

            // Eliminar la categoría
            $categoria->delete();

            return response()->json([
                'message' => 'Categoría eliminada con éxito.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            // Captura si la categoría no existe en la base de datos
            return response()->json([
                'error' => 'Categoría no encontrada.',
                'message' => 'No se pudo encontrar la categoría con el ID proporcionado.',
            ], 404);
        } catch (\Exception $e) {
            // Captura cualquier otro tipo de error
            return response()->json([
                'error' => 'Error al eliminar la categoría.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
