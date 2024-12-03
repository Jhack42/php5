<?php

namespace App\Http\Controllers;

use App\Models\BakiCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class BakiCategoriaController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        try {
            $categorias = BakiCategoria::all();

            return response()->json($categorias);
        } catch (\Exception $e) {
            Log::error('Error al obtener las categorías: '.$e->getMessage());

            return response()->json(['error' => 'Error al obtener las categorías.'], 500);
        }
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        // Verifica qué datos están llegando al controlador
        Log::debug('Datos recibidos para crear la categoría: ', $request->all());

        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'v_titulo' => 'required|string|max:255',  // Asegura que el título sea una cadena y tenga un tamaño máximo
        ]);

        // Si la validación falla, retorna un error 400 con los detalles
        if ($validator->fails()) {
            Log::error('Errores de validación al crear categoría: ', $validator->errors()->toArray());

            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            // Crear la nueva categoría
            $categoria = BakiCategoria::create([
                'v_titulo' => $request->input('v_titulo'),
            ]);

            // Log de la categoría creada
            Log::info('Categoría creada con éxito: ', ['categoria' => $categoria]);

            // Respuesta exitosa
            return response()->json($categoria, 201);
        } catch (\Exception $e) {
            // Captura cualquier error que ocurra durante la creación
            Log::error('Error al crear la categoría: '.$e->getMessage());

            // Responde con un error general
            return response()->json(['error' => 'Error al crear la categoría.'], 500);
        }
    }

    // Actualizar una categoría existente
    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'v_titulo' => 'required|string|max:50',  // Asegura que el título sea válido
        ]);

        if ($validator->fails()) {
            Log::error('Errores de validación al actualizar categoría: ', $validator->errors()->toArray());

            return response()->json(['errors' => $validator->errors()], 400);
        }

        try {
            // Encuentra la categoría por ID y actualiza sus datos
            $categoria = BakiCategoria::findOrFail($id);
            $categoria->update($request->all());

            // Log de la categoría actualizada
            Log::info('Categoría actualizada con éxito: ', ['categoria' => $categoria]);

            return response()->json($categoria);
        } catch (\Exception $e) {
            // Captura cualquier error que ocurra durante la actualización
            Log::error('Error al actualizar la categoría: '.$e->getMessage());

            return response()->json(['error' => 'Error al actualizar la categoría.'], 500);
        }
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        try {
            // Encuentra la categoría por ID y la elimina
            $categoria = BakiCategoria::findOrFail($id);
            $categoria->delete();

            // Log de la categoría eliminada
            Log::info('Categoría eliminada con éxito: ', ['categoria_id' => $id]);

            return response()->json(null, 204);  // No content
        } catch (\Exception $e) {
            // Captura cualquier error que ocurra durante la eliminación
            Log::error('Error al eliminar la categoría: '.$e->getMessage());

            return response()->json(['error' => 'Error al eliminar la categoría.'], 500);
        }
    }
}
