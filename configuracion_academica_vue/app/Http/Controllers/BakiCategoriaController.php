<?php

namespace App\Http\Controllers;

use App\Models\BakiCategoria;
use Illuminate\Http\Request;

class BakiCategoriaController extends Controller
{
    // Mostrar todas las categorías
    public function index()
    {
        try {
            return response()->json(BakiCategoria::all(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Mostrar una categoría específica
    public function show($id)
    {
        try {
            $categoria = BakiCategoria::findOrFail($id);
            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Categoría no encontrada'], 404);
        }
    }

    // Crear una nueva categoría
    public function store(Request $request)
    {
        try {
            $categoria = BakiCategoria::create($request->all());
            return response()->json($categoria, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Actualizar una categoría existente
    public function update(Request $request, $id)
    {
        try {
            $categoria = BakiCategoria::findOrFail($id);
            $categoria->update($request->all());
            return response()->json($categoria);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Eliminar una categoría
    public function destroy($id)
    {
        try {
            $categoria = BakiCategoria::findOrFail($id);
            $categoria->delete();
            return response()->json(['message' => 'Categoría eliminada con éxito']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
