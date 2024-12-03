<?php

namespace App\Http\Controllers;

use App\Models\SubCategoria;
use Illuminate\Http\Request;

class SubCategoriaController extends Controller
{
    // Crear una nueva subcategoría
    public function store(Request $request)
    {
        $validated = $request->validate([
            'n_id_categoria' => 'required|exists:categorias,n_id_categoria',
            'v_descripcion' => 'required|string|max:255',
        ]);

        $subCategoria = SubCategoria::create($validated);

        return response()->json(['message' => 'Subcategoría creada correctamente', 'subCategoria' => $subCategoria], 201);
    }

    // Actualizar una subcategoría
    public function update(Request $request, $id)
    {
        $subCategoria = SubCategoria::find($id);

        if (! $subCategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $validated = $request->validate([
            'v_descripcion' => 'required|string|max:255',
        ]);

        $subCategoria->update($validated);

        return response()->json(['message' => 'Subcategoría actualizada correctamente', 'subCategoria' => $subCategoria]);
    }

    // Eliminar una subcategoría
    public function destroy($id)
    {
        $subCategoria = SubCategoria::find($id);

        if (! $subCategoria) {
            return response()->json(['message' => 'Subcategoría no encontrada'], 404);
        }

        $subCategoria->delete();

        return response()->json(['message' => 'Subcategoría eliminada correctamente']);
    }

    // Buscar subcategorías por descripción
    public function search($descripcion)
    {
        $subCategorias = SubCategoria::where('v_descripcion', 'like', "%$descripcion%")->get();

        if ($subCategorias->isEmpty()) {
            return response()->json(['message' => 'No se encontraron subcategorías'], 404);
        }

        return response()->json(['message' => 'Subcategorías encontradas', 'subCategorias' => $subCategorias]);
    }

    public function index(Request $request)
    {
        $query = SubCategoria::query();

        if ($request->has('v_descripcion')) {
            $query->where('v_descripcion', 'like', '%'.$request->v_descripcion.'%');
        }

        $subCategorias = $query->get();

        return response()->json([
            'message' => 'Subcategorías obtenidas exitosamente',
            'data' => $subCategorias,
        ]);
    }
}
