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
    public function store(Request $request)
{
    try {
        // Validación de los datos de entrada
        $validated = $request->validate([
            'V_TITULO' => 'required|string|max:255',
        ]);

        // Verificar si existe una secuencia para auto-generar el ID
        $sequenceExists = $this->checkIfSequenceExists();

        // Primero, comprobamos si la secuencia existe en la base de datos
        if ($sequenceExists) {
            // Si la secuencia existe, usamos el siguiente valor de la secuencia
            $newId = $this->getNextSequenceValue();

            // Crear la categoría utilizando la secuencia para el ID
            $category = new SmtrCategoria();
            $category->N_ID_CATEGORIA = $newId;  // Asigna el ID de la secuencia
            $category->V_TITULO = $validated['V_TITULO'];  // Asigna el título
            $category->save();  // Guarda la nueva categoría
            return response()->json($category, 201);  // Devuelve la categoría creada

        // Si no existe la secuencia, se puede tener otra condición, por ejemplo,
        // si decidimos manejar algún otro tipo de lógica como el fallback de auto-incremento
        } elseif ($this->shouldUseAutoIncrementFallback()) {
            // Si alguna condición alternativa indica que debemos manejar el ID manualmente (auto-incremento simulado)
            $maxId = \DB::table('OMESA.SMTR_CATEGORIA')->max('N_ID_CATEGORIA');
            $newId = $maxId + 1;  // Generar el siguiente ID manualmente

            // Crear la categoría con el ID manual
            $category = new SmtrCategoria();
            $category->N_ID_CATEGORIA = $newId;  // Asigna el ID manualmente
            $category->V_TITULO = $validated['V_TITULO'];  // Asigna el título
            $category->save();  // Guarda la nueva categoría con el ID manual
            return response()->json($category, 201);  // Devuelve la categoría creada

        // Si no existe la secuencia y no se debe usar auto-incremento, simplemente retornamos un error
        } else {
            return response()->json(['error' => 'No se pudo crear la categoría. Ningún método de asignación de ID disponible.'], 500);
        }

    } catch (\Exception $e) {
        // Manejo de errores generales y detallados
        return response()->json(['error' => 'Error al crear la categoría: ' . $e->getMessage()], 500);
    }
}

/**
 * Verifica si la tabla tiene una secuencia asociada en Oracle.
 * Esto es necesario porque en Oracle se usa una secuencia para gestionar valores incrementales.
 */
private function checkIfSequenceExists()
{
    // Consulta para verificar si existe una secuencia en Oracle
    $sequence = \DB::select("SELECT SEQUENCE_NAME FROM USER_SEQUENCES WHERE SEQUENCE_NAME = 'SMTR_CATEGORIA_SEQ'");

    return count($sequence) > 0; // Devuelve true si la secuencia existe
}

/**
 * Obtiene el siguiente valor de la secuencia SMTR_CATEGORIA_SEQ.
 */
private function getNextSequenceValue()
{
    // Ejecuta una consulta para obtener el siguiente valor de la secuencia de Oracle
    $nextVal = \DB::select("SELECT SMTR_CATEGORIA_SEQ.NEXTVAL AS next_id FROM DUAL");

    return $nextVal[0]->next_id;  // Devuelve el siguiente valor de la secuencia
}

/**
 * Método adicional para decidir si usar un "fallback" para el auto-incremento simulado.
 * Este método podría basarse en alguna otra condición específica que tú definas.
 */
private function shouldUseAutoIncrementFallback()
{
    // Aquí puedes agregar condiciones para decidir si usar el ID manualmente,
    // por ejemplo, si la tabla no tiene secuencia o alguna configuración especial.
    // En este caso, simplemente devolvemos true para simular el auto-incremento.

    return true;  // Para este ejemplo, estamos asumiendo que siempre se utilizará el fallback
}

}
