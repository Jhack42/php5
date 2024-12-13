<?php

namespace App\Http\Controllers\Omesa;

use App\Http\Controllers\Controller; // Importa la clase base Controller
use App\Models\Omesa\SmtrCategoria;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log; // Asegúrate de importar la clase Log

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = SmtrCategoria::all();
        return response()->json($categorias);
    }

    public function store(Request $request)
{
    try {
        // Validación de los datos de entrada
        $validated = $request->validate([
            'v_titulo' => 'required|string|max:255',
        ]);

        // Obtener el último ID insertado en la tabla
        $maxId = \DB::table('OMESA.SMTR_CATEGORIA')->max('N_ID_CATEGORIA');

        // Asignar el próximo ID incrementando el máximo actual
        $newId = $maxId + 1;

        // Crear la nueva categoría con el ID manualmente asignado
        $category = new SmtrCategoria();
        $category->N_ID_CATEGORIA = $newId;  // Asigna el ID manualmente
        $category->V_TITULO = $validated['v_titulo']; // Asigna el título
        $category->save();  // Guarda la nueva categoría en la base de datos

        return response()->json($category, 201);  // Respuesta de éxito con los datos de la categoría creada
    } catch (\Exception $e) {
        // Manejo de errores
        return response()->json(['error' => $e->getMessage()], 500);  // Respuesta de error
    }
}


public function update(Request $request, $id)
{
    // Validación de los datos de entrada
    $validator = Validator::make($request->all(), [
        'v_titulo' => 'required|string|max:50', // Solo validamos 'v_titulo' aquí
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400); // Retorna los errores de validación si los hay
    }

    // Encontramos la categoría por su ID
    $categoria = SmtrCategoria::findOrFail($id);

    // Solo actualizamos el campo 'v_titulo' y no modificamos 'N_ID_CATEGORIA'
    $categoria->v_titulo = $request->input('v_titulo');

    // Guardamos los cambios
    $categoria->save();

    // Devolvemos la categoría actualizada
    return response()->json($categoria);
}


    public function destroy($id)
    {
        $categoria = SmtrCategoria::findOrFail($id);
        $categoria->delete();
        return response()->json(null, 204);
    }
}
