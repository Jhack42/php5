<?php

namespace App\Http\Controllers;

use App\Models\Categoria; // Importar el modelo
use App\Models\SubCategoria; // Importa el modelo SubCategoria
use Illuminate\Http\Request;

class SubCategoriaTestController extends Controller
{
    // Método para probar la creación de una nueva subcategoría
    public function testNuevo()
    {
        $n_id_categoria = 2; // ID de categoría
        $v_descripcion = 'Subcategoría de prueba';

        // Verifica si la categoría existe
        if (! Categoria::find($n_id_categoria)) {
            return response()->json(['message' => 'La categoría especificada no existe'], 400);
        }

        $nuevaSubCategoria = [
            'n_id_categoria' => $n_id_categoria,
            'v_descripcion' => $v_descripcion,
        ];

        $subCategoria = SubCategoria::create($nuevaSubCategoria);

        if ($subCategoria) {
            return response()->json(['message' => 'Subcategoría creada correctamente', 'subCategoria' => $subCategoria], 201);
        } else {
            return response()->json(['message' => 'Error al crear la subcategoría'], 500);
        }
    }

    // Método para probar la edición de una subcategoría
    public function testEditar($id)
    {
        $subCategoria = SubCategoria::find($id);

        if ($subCategoria) {
            $subCategoria->update([
                'v_descripcion' => 'Subcategoría actualizada',
            ]);

            return response()->json(['message' => 'Subcategoría actualizada correctamente', 'subCategoria' => $subCategoria]);
        } else {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }
    }

    // Método para probar la eliminación de una subcategoría
    public function testEliminar(Request $request, $id)
    {
        $subCategoria = SubCategoria::find($id);

        if ($subCategoria) {
            $subCategoria->delete();

            return response()->json(['message' => 'Subcategoría eliminada correctamente']);
        } else {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }
    }

    // Método para probar la búsqueda de subcategorías por descripción
    public function testBuscar($descripcion)
    {
        $subCategorias = SubCategoria::where('v_descripcion', 'like', "%$descripcion%")->get();

        if ($subCategorias->count() > 0) {
            return response()->json(['message' => 'Subcategorías encontradas', 'subCategorias' => $subCategorias]);
        } else {
            return response()->json(['message' => 'No se encontraron subcategorías'], 404);
        }
    }
}
