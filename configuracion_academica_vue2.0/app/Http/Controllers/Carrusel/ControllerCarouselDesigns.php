<?php

namespace App\Http\Controllers\Carrusel;

use App\Http\Controllers\Controller;
use App\Models\Carrusel\ModelCarouselDesigns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ControllerCarouselDesigns extends Controller
{
    /**
     * Listar todos los diseños con sus relaciones
     */
    public function index()
    {
        try {
            $designs = ModelCarouselDesigns::with(['imagen', 'video'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Diseños obtenidos correctamente',
                'data' => $designs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener diseños: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear nuevo diseño
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // Validar request
            $request->validate([
                'name' => 'required|string|max:100',
                'id_imagen' => 'nullable|exists:imagenes,id',
                'id_video' => 'nullable|exists:videos,id',
                'custom_css' => 'nullable|string'
            ]);

            $design = ModelCarouselDesigns::create($request->all());

            DB::commit();

            // Cargar las relaciones
            $design->load(['imagen', 'video']);

            return response()->json([
                'success' => true,
                'message' => 'Diseño creado correctamente',
                'data' => $design,
                'generated_css' => $design->generateCSS()
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear diseño: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Actualizar diseño
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validar request
            $request->validate([
                'name' => 'sometimes|required|string|max:100',
                'id_imagen' => 'nullable|exists:imagenes,id',
                'id_video' => 'nullable|exists:videos,id',
                'custom_css' => 'nullable|string'
            ]);

            $design = ModelCarouselDesigns::findOrFail($id);
            $design->update($request->all());

            DB::commit();

            // Cargar las relaciones actualizadas
            $design->load(['imagen', 'video']);

            return response()->json([
                'success' => true,
                'message' => 'Diseño actualizado correctamente',
                'data' => $design,
                'updated_css' => $design->generateCSS()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar diseño: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar diseño
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $design = ModelCarouselDesigns::with(['imagen', 'video'])->findOrFail($id);

            // Guardar información antes de eliminar
            $deletedInfo = [
                'id' => $design->id_carousel_design,
                'name' => $design->name,
                'imagen' => $design->imagen ? $design->imagen->name : null,
                'video' => $design->video ? $design->video->name : null
            ];

            $design->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Diseño eliminado correctamente',
                'deleted_data' => array_merge($deletedInfo, ['deleted_at' => now()])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el diseño',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener diseño específico con sus relaciones
     */
    public function show($id)
    {
        try {
            $design = ModelCarouselDesigns::with(['imagen', 'video'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Diseño obtenido correctamente',
                'data' => $design
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el diseño: ' . $e->getMessage()
            ], 500);
        }
    }
}
