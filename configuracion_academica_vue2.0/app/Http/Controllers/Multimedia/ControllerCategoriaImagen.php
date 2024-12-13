<?php
// ControllerCategoriaImagen.php
namespace App\Http\Controllers\Multimedia;

use App\Http\Controllers\Controller;
use App\Models\Multimedia\CategoriaImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ControllerCategoriaImagen extends Controller
{
    public function index()
    {
        try {
            $categorias = CategoriaImagen::with('imagenes')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Categorías obtenidas correctamente',
                'data' => $categorias
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener categorías: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'categoria' => 'required|string|max:100|unique:categoria_imagen'
            ]);

            $categoria = CategoriaImagen::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría creada correctamente',
                'data' => $categoria
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'categoria' => 'required|string|max:100|unique:categoria_imagen,categoria,' . $id . ',id_categoria_imagen'
            ]);

            $categoria = CategoriaImagen::findOrFail($id);
            $categoria->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Categoría actualizada correctamente',
                'data' => $categoria
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar categoría: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $categoria = CategoriaImagen::findOrFail($id);

            if ($categoria->imagenes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene imágenes asociadas'
                ], 400);
            }

            $categoria->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar categoría: ' . $e->getMessage()
            ], 500);
        }
    }
}
