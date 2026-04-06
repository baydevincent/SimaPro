<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShopDrawing;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ShopDrawingController extends Controller
{
    /**
     * Display a listing of shop drawings for a project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $shopDrawings = ShopDrawing::where('project_id', $project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $shopDrawings->map(function ($drawing) {
                return [
                    'id' => $drawing->id,
                    'nama_file_asli' => $drawing->nama_file_asli,
                    'nama_file' => $drawing->nama_file,
                    'file_size' => $drawing->formatted_file_size,
                    'created_at' => $drawing->created_at->format('d M Y H:i'),
                    'uploaded_by' => $drawing->uploaded_by,
                    'file_url' => $drawing->file_url,
                    'is_image' => $drawing->is_image,
                    'is_pdf' => $drawing->is_pdf,
                    'deskripsi' => $drawing->deskripsi,
                ];
            }),
        ]);
    }

    /**
     * Store a newly uploaded shop drawing.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240',
            'deskripsi' => 'nullable|string|max:500',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded',
            ], 400);
        }

        $uploadedFile = $request->file('file');

        $originalName = $uploadedFile->getClientOriginalName();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $uploadedFile->getClientOriginalExtension();
        $newFileName = $fileName . '_' . time() . '.' . $extension;

        $filePath = $uploadedFile->storeAs(
            'shop-drawings/' . $project->id,
            $newFileName,
            'public'
        );

        $shopDrawing = ShopDrawing::create([
            'project_id' => $project->id,
            'nama_file' => $newFileName,
            'nama_file_asli' => $originalName,
            'file_path' => $filePath,
            'file_mime_type' => $uploadedFile->getMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'deskripsi' => $validated['deskripsi'] ?? null,
            'uploaded_by' => Auth::check() ? Auth::user()->name : 'Unknown',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shop drawing uploaded successfully',
            'data' => [
                'id' => $shopDrawing->id,
                'nama_file_asli' => $shopDrawing->nama_file_asli,
                'file_size' => $shopDrawing->formatted_file_size,
                'created_at' => $shopDrawing->created_at->format('d M Y H:i'),
                'uploaded_by' => $shopDrawing->uploaded_by,
                'file_url' => $shopDrawing->file_url,
                'is_image' => $shopDrawing->is_image,
                'is_pdf' => $shopDrawing->is_pdf,
            ],
        ], 201);
    }

    /**
     * Delete a shop drawing.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $shopDrawing
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project, $shopDrawing): JsonResponse
    {
        $shopDrawingModel = ShopDrawing::where('id', $shopDrawing)
            ->where('project_id', $project->id)
            ->firstOrFail();

        if (Storage::disk('public')->exists($shopDrawingModel->file_path)) {
            Storage::disk('public')->delete($shopDrawingModel->file_path);
        }

        $shopDrawingModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop drawing deleted successfully',
        ]);
    }
}
