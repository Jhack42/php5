<?php

namespace App\Http\Controllers;

use App\Models\Categoria; // Asegúrate de importar el modelo
use Illuminate\Http\Request;

class CategoriaTestController extends Controller
{
    // Método para probar la creación de una nueva categoría
    public function testNuevo()
    {
        $nuevaCategoria = [
            'v_titulo' => 'Categoría de prueba',
        ];

        $categoria = Categoria::create($nuevaCategoria);

        if ($categoria) {
            return response()->json(['message' => 'Categoría creada correctamente', 'categoria' => $categoria], 201);
        } else {
            return response()->json(['message' => 'Error al crear la categoría'], 500);
        }
    }

    public function testEditar($id)
    {
        $producto = Categoria::find($id);

        if ($producto) {
            $producto->update([
                'v_titulo' => 'Producto actualizado',
            ]);

            return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto]);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para probar la eliminación de una categoría

    public function testEliminar(Request $request, $id)
    {
        $producto = Categoria::find($id);

        if ($producto) {
            $producto->delete();

            return response()->json(['message' => 'Producto eliminado correctamente']);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para probar la búsqueda de categorías por título
    public function testBuscar($titulo)
    {
        $categorias = Categoria::where('V_TITULO', 'like', "%$titulo%")->get();

        if ($categorias->count() > 0) {
            return response()->json(['message' => 'Categorías encontradas', 'categorias' => $categorias]);
        } else {
            return response()->json(['message' => 'No se encontraron categorías'], 404);
        }
    }
}
