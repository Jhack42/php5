<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria; // Importar el modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubCategoriaController extends Controller
{
    // Obtener todas las subcategorías

    public function index()
    {
        try {
            // Obtener todas las subcategorías
            $subCategorias = SubCategoria::all();

            // Retornar las subcategorías en un formato estándar
            return response()->json([
                'message' => 'Subcategorías obtenidas correctamente',
                'subCategorias' => $subCategorias,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error al obtener las subcategorías: ' . $e->getMessage());

            return response()->json(['message' => 'Error al obtener las subcategorías'], 500);
        }
    }

    // Método para probar la creación de una nueva subcategoría
    public function testNuevo(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'n_id_categoria' => 'required|integer|exists:smtr_categoria,n_id_categoria',
            'v_descripcion' => 'required|string|max:255',
        ]);

        $n_id_categoria = $request->n_id_categoria; // ID de categoría desde la solicitud
        $v_descripcion = $request->v_descripcion;   // Descripción de la subcategoría

        // Crear la nueva subcategoría
        $subCategoria = SubCategoria::create([
            'n_id_categoria' => $n_id_categoria,
            'v_descripcion' => $v_descripcion,
        ]);

        return response()->json([
            'message' => 'Subcategoría creada correctamente',
            'subCategoria' => $subCategoria,
        ], 201);
    }

    // Método para probar la edición de una subcategoría
    public function editarSubCategoria(Request $request, $id)
    {
        // Validar los datos de entrada
        $request->validate([
            'v_descripcion' => 'required|string|max:255',
        ]);

        // Buscar la subcategoría por ID
        $subCategoria = SubCategoria::find($id);

        // Si la subcategoría no existe, devolver un error
        if (!$subCategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        // Actualizar la subcategoría
        $subCategoria->update([
            'v_descripcion' => $request->v_descripcion,
        ]);

        // Retornar la respuesta de éxito con los detalles de la subcategoría actualizada
        return response()->json([
            'message' => 'Subcategoría actualizada correctamente',
            'subCategoria' => $subCategoria,
        ]);
    }

    // Método para probar la eliminación de una subcategoría
    // Método para eliminar una subcategoría
    public function eliminar($id)
    {
        $subCategoria = SubCategoria::find($id);

        if ($subCategoria) {
            $subCategoria->delete(); // Eliminar la subcategoría

            return response()->json(['message' => 'Subcategoría eliminada correctamente']);
        } else {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }
    }

    // Método para probar la búsqueda de subcategorías por descripción
    public function testBuscar($descripcion)
    {
        // Buscar subcategorías que coincidan con la descripción
        $subCategorias = SubCategoria::where('v_descripcion', 'like', "%$descripcion%")->get();

        if ($subCategorias->isEmpty()) {
            return response()->json(['message' => 'No se encontraron subcategorías'], 404);
        }

        return response()->json([
            'message' => 'Subcategorías encontradas',
            'subCategorias' => $subCategorias,
        ]);
    }
}
