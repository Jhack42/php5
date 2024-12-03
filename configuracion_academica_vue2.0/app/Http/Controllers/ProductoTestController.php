<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoTestController extends Controller
{
    // Método para probar la creación de un nuevo producto
    public function testNuevo()
    {
        $nuevoProducto = [
            'nombre' => 'Producto de prueba',
            'descripcion' => 'Descripción del producto de prueba',
            'precio' => 99.99,
            'stock' => 10,
            'fecha_expiracion' => '2025-12-31',
        ];

        $producto = Producto::create($nuevoProducto);

        if ($producto) {
            return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto], 201);
        } else {
            return response()->json(['message' => 'Error al crear el producto'], 500);
        }
    }

    // Método para probar la edición de un producto existente
    public function testEditar($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->update([
                'nombre' => 'Producto actualizado',
                'precio' => 199.99,
                'stock' => 5,
            ]);

            return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto]);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para probar la eliminación de un producto
    public function testEliminar(Request $request, $id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->delete();

            return response()->json(['message' => 'Producto eliminado correctamente']);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para probar la búsqueda de productos por nombre
    public function testBuscar($nombre)
    {
        $productos = Producto::where('id', $nombre)
            ->orWhere('nombre', 'like', "%$nombre%")
            ->get();

        if ($productos->count() > 0) {
            return response()->json(['message' => 'Productos encontrados', 'productos' => $productos]);
        } else {
            return response()->json(['message' => 'No se encontraron productos'], 404);
        }
    }
}
