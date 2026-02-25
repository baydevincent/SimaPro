<?php

namespace App\Http\Controllers;

use App\Models\ShopDrawing;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ShopDrawingController extends Controller
{
    /**
     * Display a listing of shop drawings for a project.
     */
    public function index(Project $project)
    {
        $shopDrawings = ShopDrawing::byProject($project->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('shopdrawing.index', compact('project', 'shopDrawings'));
    }

    /**
     * Store a newly uploaded shop drawing.
     */
    public function store(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // Max 10MB
            'deskripsi' => 'nullable|string|max:500',
        ], [
            'file.required' => 'File shop drawing wajib diupload',
            'file.file' => 'File tidak valid',
            'file.max' => 'Ukuran file maksimal 10MB',
        ]);

        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diupload'
            ], 400);
        }

        $uploadedFile = $request->file('file');
        
        // Generate unique filename
        $originalName = $uploadedFile->getClientOriginalName();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $uploadedFile->getClientOriginalExtension();
        $newFileName = $fileName . '_' . time() . '.' . $extension;
        
        // Store file in storage/app/public/shop-drawings/{project_id}
        $filePath = $uploadedFile->storeAs(
            'shop-drawings/' . $project->id,
            $newFileName,
            'public'
        );

        // Create database record
        $shopDrawing = ShopDrawing::create([
            'project_id' => $project->id,
            'nama_file' => $newFileName,
            'nama_file_asli' => $originalName,
            'file_path' => $filePath,
            'file_mime_type' => $uploadedFile->getMimeType(),
            'file_size' => $uploadedFile->getSize(),
            'deskripsi' => $request->input('deskripsi'),
            'uploaded_by' => Auth::check() ? Auth::user()->name : 'Unknown',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shop drawing berhasil diupload',
            'data' => [
                'id' => $shopDrawing->id,
                'nama_file_asli' => $shopDrawing->nama_file_asli,
                'file_size' => $shopDrawing->formatted_file_size,
                'created_at' => $shopDrawing->created_at->format('d M Y H:i'),
                'uploaded_by' => $shopDrawing->uploaded_by,
                'file_url' => $shopDrawing->file_url,
                'is_image' => $shopDrawing->is_image,
                'is_pdf' => $shopDrawing->is_pdf,
            ]
        ]);
    }

    /**
     * Download a shop drawing.
     */
    public function download(Project $project, $shopDrawingId)
    {
        $shopDrawing = ShopDrawing::findOrFail($shopDrawingId);
        
        // Verify the shop drawing belongs to this project
        if ($shopDrawing->project_id != $project->id) {
            abort(403, 'Unauthorized access');
        }

        if (!Storage::disk('public')->exists($shopDrawing->file_path)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($shopDrawing->file_path, $shopDrawing->nama_file_asli);
    }

    /**
     * Delete a shop drawing.
     */
    public function destroy(Project $project, $shopDrawingId)
    {
        $shopDrawing = ShopDrawing::findOrFail($shopDrawingId);
        
        // Verify the shop drawing belongs to this project
        if ($shopDrawing->project_id != $project->id) {
            abort(403, 'Unauthorized access');
        }

        // Delete file from storage
        if (Storage::disk('public')->exists($shopDrawing->file_path)) {
            Storage::disk('public')->delete($shopDrawing->file_path);
        }

        // Delete database record
        $shopDrawing->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shop drawing berhasil dihapus'
        ]);
    }
}
