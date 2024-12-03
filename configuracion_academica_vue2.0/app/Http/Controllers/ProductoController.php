<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    // Método para mostrar todos los productos
    public function index()
    {
        $productos = Producto::all();

        return response()->json($productos);
    }

    // Método para almacenar un nuevo producto
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:500',
            'precio' => 'required|numeric',
            'stock' => 'required|integer',
            'fecha_expiracion' => 'nullable|date',
        ]);

        $producto = Producto::create($request->all());

        if ($producto) {
            return response()->json(['message' => 'Producto creado correctamente', 'producto' => $producto], 201);
        } else {
            return response()->json(['message' => 'Error al crear el producto'], 500);
        }
    }

    // Método para editar un producto existente
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->update($request->all());

            return response()->json(['message' => 'Producto actualizado correctamente', 'producto' => $producto]);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para eliminar un producto
    public function destroy($id)
    {
        $producto = Producto::find($id);

        if ($producto) {
            $producto->delete();

            return response()->json(['message' => 'Producto eliminado correctamente']);
        } else {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    }

    // Método para buscar productos por nombre o ID
    public function search(Request $request)
    {
        $searchTerm = $request->input('search');

        $productos = Producto::where('id', $searchTerm)
            ->orWhere('nombre', 'like', "%$searchTerm%")
            ->get();

        if ($productos->count() > 0) {
            return response()->json(['message' => 'Productos encontrados', 'productos' => $productos]);
        } else {
            return response()->json(['message' => 'No se encontraron productos'], 404);
        }
    }
}
