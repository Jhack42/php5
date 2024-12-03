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
    try {
        // Validación de los datos de entrada
        $validated = $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        // Verificar si existe una secuencia para auto-generar el ID
        $sequenceExists = $this->checkIfSequenceExists();

        if ($sequenceExists) {
            // Usamos la secuencia para generar el ID (solo si existe la secuencia)
            $newId = $this->getNextSequenceValue();

            // Crear la categoría usando la secuencia para el ID
            $category = new SmtrCategoria();
            $category->N_ID_CATEGORIA = $newId;  // Asigna el ID de la secuencia
            $category->V_TITULO = $validated['V_TITULO'];  // Asigna el título
            $category->save();  // Guarda la nueva categoría
            return response()->json($category, 201);  // Devuelve la categoría creada

        } else {
            // Si no existe una secuencia, asignamos el ID manualmente
            // Obtener el máximo ID actual y asignar el siguiente ID manual
            $maxId = \DB::table('OMESA.SMTR_CATEGORIA')->max('N_ID_CATEGORIA');
            $newId = $maxId + 1;

            // Crear la categoría con el ID manual
            $category = new SmtrCategoria();
            $category->N_ID_CATEGORIA = $newId;  // Asigna el ID manualmente
            $category->V_TITULO = $validated['V_TITULO'];  // Asigna el título
            $category->save();  // Guarda la nueva categoría con el ID manual
            return response()->json($category, 201);  // Devuelve la categoría creada
        }

    } catch (\Exception $e) {
        // Manejo de errores generales y detallados
        return response()->json(['error' => 'Error al crear la categoría: ' . $e->getMessage()], 500);
    }
}


    /**
     * Actualizar un registro en SMTR_CATEGORIA.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos de entrada
        $request->validate([
            'V_TITULO' => 'required|string|max:50', // Asegura que el título no exceda los 50 caracteres
        ]);

        try {
            // Buscar el registro por ID
            $category = SmtrCategoria::findOrFail($id);
            $category->V_TITULO = $request->V_TITULO; // Actualizar el título
            $category->save(); // Guardar los cambios

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
            // Buscar el registro por ID
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
