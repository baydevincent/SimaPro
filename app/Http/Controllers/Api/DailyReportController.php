<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\DailyReportImage;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class DailyReportController extends Controller
{
    /**
     * Display a listing of daily reports for a project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $reports = DailyReport::where('project_id', $project->id)
            ->with(['images', 'creator'])
            ->orderBy('tanggal', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'reports' => $reports->items(),
                'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created daily report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'uraian_kegiatan' => 'required|string',
            'cuaca' => 'nullable|string|max:255',
            'jumlah_pekerja' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'nullable|string|max:255',
        ]);

        $report = DailyReport::create([
            'project_id' => $project->id,
            'created_by' => auth('api')->id(),
            'tanggal' => $validated['tanggal'],
            'uraian_kegiatan' => $validated['uraian_kegiatan'],
            'cuaca' => $validated['cuaca'] ?? null,
            'jumlah_pekerja' => $validated['jumlah_pekerja'] ?? 0,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('daily-reports/' . $project->id, 'public');

                DailyReportImage::create([
                    'daily_report_id' => $report->id,
                    'image_path' => $path,
                    'caption' => $request->captions[$index] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Daily report created successfully',
            'data' => $report->fresh(['images', 'creator']),
        ], 201);
    }

    /**
     * Display the specified daily report.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project, $report): JsonResponse
    {
        $reportModel = DailyReport::where('id', $report)
            ->where('project_id', $project->id)
            ->with(['images', 'creator', 'project'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $reportModel,
        ]);
    }

    /**
     * Update the specified daily report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @param  int  $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Project $project, $report): JsonResponse
    {
        $reportModel = DailyReport::where('id', $report)
            ->where('project_id', $project->id)
            ->firstOrFail();

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'uraian_kegiatan' => 'required|string',
            'cuaca' => 'nullable|string|max:255',
            'jumlah_pekerja' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'nullable|string|max:255',
        ]);

        $reportModel->update([
            'tanggal' => $validated['tanggal'],
            'uraian_kegiatan' => $validated['uraian_kegiatan'],
            'cuaca' => $validated['cuaca'] ?? $reportModel->cuaca,
            'jumlah_pekerja' => $validated['jumlah_pekerja'] ?? $reportModel->jumlah_pekerja,
            'catatan' => $validated['catatan'] ?? $reportModel->catatan,
        ]);

        // Handle new image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('daily-reports/' . $project->id, 'public');

                DailyReportImage::create([
                    'daily_report_id' => $reportModel->id,
                    'image_path' => $path,
                    'caption' => $request->captions[$index] ?? null,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Daily report updated successfully',
            'data' => $reportModel->fresh(['images', 'creator']),
        ]);
    }

    /**
     * Remove the specified daily report.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project, $report): JsonResponse
    {
        $reportModel = DailyReport::where('id', $report)
            ->where('project_id', $project->id)
            ->firstOrFail();

        // Delete associated images
        foreach ($reportModel->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $reportModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Daily report deleted successfully',
        ]);
    }

    /**
     * Download daily report as PDF.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function downloadPdf(Project $project, $report): JsonResponse
    {
        $reportModel = DailyReport::where('id', $report)
            ->where('project_id', $project->id)
            ->with(['images', 'creator', 'project'])
            ->firstOrFail();

        // Return PDF download URL or generate PDF
        return response()->json([
            'success' => true,
            'message' => 'PDF generation not implemented in API',
            'data' => $reportModel,
        ]);
    }
}
