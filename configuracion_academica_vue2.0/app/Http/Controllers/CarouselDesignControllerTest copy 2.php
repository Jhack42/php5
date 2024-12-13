<?php

namespace App\Http\Controllers;

use App\Models\PreviewFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CarouselDesignController extends Controller
{
    public function index()
    {
        try {
            $designs = PreviewFinal::orderBy('created_at', 'desc')->get();
            return response()->json([
                'success' => true,
                'data' => $designs
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los diseños',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'width_type' => 'required|in:responsive,fixed,fluid',
                'width_value' => 'nullable|string|max:50',
                'height_type' => 'required|in:fixed,aspect-ratio',
                'height_value' => 'required|string|max:50',
                'background_type' => 'required|in:color,image,video',
                'background_color' => 'nullable|string|max:20',
                'background_image' => 'nullable|string|max:255',
                'background_video' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $design = PreviewFinal::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Diseño creado exitosamente',
                'data' => $design
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el diseño',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $design = PreviewFinal::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $design
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Diseño no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $design = PreviewFinal::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'string|max:100',
                'width_type' => 'in:responsive,fixed,fluid',
                'width_value' => 'nullable|string|max:50',
                'height_type' => 'in:fixed,aspect-ratio',
                'height_value' => 'string|max:50',
                'background_type' => 'in:color,image,video',
                'background_color' => 'nullable|string|max:20',
                'background_image' => 'nullable|string|max:255',
                'background_video' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $design->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Diseño actualizado exitosamente',
                'data' => $design
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el diseño',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $design = PreviewFinal::findOrFail($id);

            // Eliminar archivos asociados si existen
            if ($design->background_image) {
                Storage::delete($design->background_image);
            }
            if ($design->background_video) {
                Storage::delete($design->background_video);
            }

            $design->delete();

            return response()->json([
                'success' => true,
                'message' => 'Diseño eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el diseño',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadImage(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $path = $request->file('image')->store('carousel-images', 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir la imagen',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadVideo(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'video' => 'required|mimetypes:video/mp4,video/quicktime|max:20480'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $path = $request->file('video')->store('carousel-videos', 'public');

            return response()->json([
                'success' => true,
                'url' => Storage::url($path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al subir el video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getActiveDesign()
    {
        try {
            $design = PreviewFinal::where('active', 1)->first();

            if (!$design) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay diseño activo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $design,
                'css' => $design->generateCSS()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el diseño activo',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

