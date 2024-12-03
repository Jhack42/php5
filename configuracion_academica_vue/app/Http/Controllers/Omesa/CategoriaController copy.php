<?php

namespace App\Http\Controllers\Omesa;

use App\Http\Controllers\Controller; // Importa la clase base Controller
use App\Models\Omesa\SmtrCategoria;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Log; // Asegúrate de importar la clase Log

class CategoriaController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        $categorias = SmtrCategoria::all(); // Obtiene todas las categorías
        return response()->json($categorias); // Retorna los datos en formato JSON
    }

    // Almacenar una nueva categoría
    public function store(Request $request)
    {
        // Validación de los datos entrantes
        $validator = Validator::make($request->all(), [
            'V_TITULO' => 'required|string|max:50', // Validamos el campo 'V_TITULO'
        ]);

        // Si la validación falla, retornamos los errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Creamos la nueva categoría con los datos válidos
        $categoria = SmtrCategoria::create($request->all());

        // Retornamos la categoría creada con un código de estado 201 (creado)
        return response()->json($categoria, 201);
    }

    // Actualizar una categoría existente
    public function update(Request $request, $id)
    {
        // Validación de los datos entrantes
        $validator = Validator::make($request->all(), [
            'V_TITULO' => 'required|string|max:50', // Validamos el campo 'V_TITULO'
        ]);

        // Si la validación falla, retornamos los errores
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Encontramos la categoría por su ID, o lanzamos un error 404 si no se encuentra
        $categoria = SmtrCategoria::findOrFail($id);

        // Actualizamos la categoría con los nuevos datos
        $categoria->update($request->all());

        // Retornamos la categoría actualizada
        return response()->json($categoria);
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        // Encontramos la categoría por su ID, o lanzamos un error 404 si no se encuentra
        $categoria = SmtrCategoria::findOrFail($id);

        // Eliminamos la categoría
        $categoria->delete();

        // Retornamos una respuesta con código de estado 204 (sin contenido)
        return response()->json(null, 204);
    }
}
