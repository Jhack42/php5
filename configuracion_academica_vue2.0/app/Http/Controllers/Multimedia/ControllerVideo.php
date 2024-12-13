<?php
namespace App\Http\Controllers\Multimedia;

use App\Http\Controllers\Controller;
use App\Models\Multimedia\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ControllerVideo extends Controller
{
    public function index()
    {
        try {
            $videos = Video::with('categoria')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Videos obtenidos correctamente',
                'data' => $videos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener videos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'required|string|max:200',
                'descripcion' => 'nullable|string',
                'video' => 'required|string',
                'id_categoria_video' => 'required|exists:categoria_video,id_categoria_video'
            ]);

            $video = Video::create($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Video creado correctamente',
                'data' => $video
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear video: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'name' => 'sometimes|required|string|max:200',
                'descripcion' => 'nullable|string',
                'video' => 'sometimes|required|string',
                'id_categoria_video' => 'sometimes|required|exists:categoria_video,id_categoria_video'
            ]);

            $video = Video::findOrFail($id);
            $video->update($request->all());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Video actualizado correctamente',
                'data' => $video
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar video: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $video = Video::findOrFail($id);

            if ($video->carousels()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el video porque está siendo utilizado en carruseles'
                ], 400);
            }

            $video->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Video eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un video específico con su categoría
     */
    public function show($id)
    {
        try {
            $video = Video::with('categoria')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Video obtenido correctamente',
                'data' => $video
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener video: ' . $e->getMessage()
            ], 500);
        }
    }
}
