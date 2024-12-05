<?php

namespace App\Http\Controllers;

use App\Models\BakiCategoria;
use Illuminate\Http\Request;

class BakiCategoriaController extends Controller
{
    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        // Crear una nueva categoría
        $categoria = BakiCategoria::create([
            'V_TITULO' => $request->V_TITULO,
        ]);

        return response()->json($categoria, 201);
    }

    // Función para actualizar el título de una categoría existente
    public function update(Request $request, $id)
    {
        // Validar los datos entrantes
        $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        // Llamar al procedimiento almacenado en Oracle
        try {
            DB::statement('BEGIN OMESA.UPDATE_BAKI_CATEGORIA(:id, :titulo); END;', [
                'id' => $id,
                'titulo' => $request->V_TITULO,
            ]);

            return response()->json(['message' => 'Categoría actualizada con éxito.']);

        } catch (\Exception $e) {
            // Si ocurre un error, devolver un mensaje adecuado
            return response()->json(['error' => 'Error al actualizar la categoría: '.$e->getMessage()], 500);
        }
    }

    // Función para eliminar una categoría por su ID
    public function destroy($id)
    {
        // Buscar la categoría por su ID
        $categoria = BakiCategoria::findOrFail($id);

        // Eliminar la categoría (el trigger de eliminación manejará lo que necesites antes de eliminarla)
        $categoria->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Categoría eliminada exitosamente'], 200);
    }

    // Función para obtener todas las categorías
    public function index()
    {
        // Obtener todas las categorías
        $categorias = BakiCategoria::all();

        // Retornar las categorías en formato JSON
        return response()->json($categorias, 200);
    }

    // Función para obtener una categoría por su ID
    public function show($id)
    {
        // Buscar la categoría por su ID
        $categoria = BakiCategoria::findOrFail($id);

        // Retornar la categoría en formato JSON
        return response()->json($categoria, 200);
    }
}
