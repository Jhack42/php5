<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\PreviewFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class CarouselDesignController extends Controller
{
    /**
     * Listar todos los diseños
     */
    public function index()
    {
        try {
            $designs = PreviewFinal::orderBy('created_at', 'desc')->get();
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
        try {
            $design = PreviewFinal::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Diseño creado correctamente',
                'data' => $design,
                'generated_css' => $design->generateCSS()
            ], 201);
        } catch (\Exception $e) {
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
        try {
            $design = PreviewFinal::findOrFail($id);
            $design->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Diseño actualizado correctamente',
                'data' => $design,
                'updated_css' => $design->generateCSS()
            ]);
        } catch (\Exception $e) {
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
            $design = PreviewFinal::findOrFail($id);

            // Guardar información antes de eliminar
            $deletedInfo = [
                'id' => $design->id_carousel_design,
                'name' => $design->name,
                'type' => $design->background_type
            ];

            // Eliminar archivos asociados si existen
            if ($design->background_image) {
                Storage::delete($design->background_image);
            }
            if ($design->background_video) {
                Storage::delete($design->background_video);
            }

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
}
