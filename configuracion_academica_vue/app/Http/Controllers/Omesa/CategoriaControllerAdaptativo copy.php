<?php

namespace App\Http\Controllers\Omesa;

use App\Http\Controllers\Controller; // Importa la clase base Controller
use App\Models\Omesa\SmtrCategoria;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log; // Asegúrate de importar la clase Log
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class CategoriaControllerAdaptativo extends Controller
{
    /**
     * Obtener todos los registros de SMTR_CATEGORIA.
     */
    public function index()
    {
        try {
            $categories = SmtrCategoria::all();
            return response()->json($categories);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching records: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Obtener un solo registro por su N_ID_CATEGORIA.
     */
    public function show($id)
    {
        try {
            $category = SmtrCategoria::findOrFail($id);
            return response()->json($category);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error fetching record: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Crear un nuevo registro en SMTR_CATEGORIA.
     */
    public function store(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        try {
            $category = new SmtrCategoria();
            $category->V_TITULO = $request->V_TITULO;
            $category->save(); // Guardamos el nuevo registro

            return response()->json(['id' => $category->N_ID_CATEGORIA], 201); // Retorna el ID generado
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating record: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Actualizar un registro en SMTR_CATEGORIA.
     */
    public function update(Request $request, $id)
    {
        // Validar los datos de entrada
        $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        try {
            $category = SmtrCategoria::findOrFail($id);
            $category->V_TITULO = $request->V_TITULO;
            $category->save(); // Guardamos los cambios

            return response()->json(['message' => 'Record updated successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating record: ' . $e->getMessage()], 400);
        }
    }

    /**
     * Eliminar un registro de SMTR_CATEGORIA.
     */
    public function destroy($id)
    {
        try {
            $category = SmtrCategoria::findOrFail($id);
            $category->delete(); // Eliminar el registro

            return response()->json(['message' => 'Record deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Category not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting record: ' . $e->getMessage()], 400);
        }
    }
}
