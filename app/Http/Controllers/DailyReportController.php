<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\DailyReportImage;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DailyReportController extends Controller
{
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $reports = DailyReport::where('project_id', $projectId)
            ->with('images')
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('daily-reports.index-tab', compact('project', 'reports'));
    }

    public function create($projectId)
    {
        $project = Project::findOrFail($projectId);
        
        // Get total workers from attendance on current date
        $totalWorkers = 0;
        $attendance = $project->attendances()
            ->whereDate('tanggal', date('Y-m-d'))
            ->first();
        
        if ($attendance) {
            $totalWorkers = $attendance->attendanceWorkers()->where('hadir', true)->count();
        }

        return view('daily-reports.create', compact('project', 'totalWorkers'));
    }

    public function store(Request $request, $projectId)
    {
        $project = Project::findOrFail($projectId);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'uraian_kegiatan' => 'required|string',
            'cuaca' => 'nullable|string|max:255',
            'jumlah_pekerja' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'nullable|string|max:255',
            'additional_captions.*' => 'nullable|string|max:255',
        ]);

        $report = DailyReport::create([
            'project_id' => $project->id,
            'created_by' => auth()->id(),
            'tanggal' => $validated['tanggal'],
            'uraian_kegiatan' => $validated['uraian_kegiatan'],
            'cuaca' => $validated['cuaca'] ?? null,
            'jumlah_pekerja' => $validated['jumlah_pekerja'] ?? 0,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $uploadedCount = 0;

        // Handle main image uploads (from multiple file input)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                if (!$image || !$image->isValid()) {
                    continue;
                }

                $path = $image->store('daily-reports/' . $project->id, 'public');

                DailyReportImage::create([
                    'daily_report_id' => $report->id,
                    'image_path' => $path,
                    'caption' => $request->captions[$index] ?? null,
                ]);
                
                $uploadedCount++;
            }
        }

        // Handle additional image uploads
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $image) {
                if (!$image || !$image->isValid()) {
                    continue;
                }

                $path = $image->store('daily-reports/' . $project->id, 'public');

                DailyReportImage::create([
                    'daily_report_id' => $report->id,
                    'image_path' => $path,
                    'caption' => $request->additional_captions[$index] ?? null,
                ]);
                
                $uploadedCount++;
            }
        }

        \Log::info('Daily Report Store', [
            'project_id' => $project->id,
            'report_id' => $report->id,
            'total_uploaded' => $uploadedCount,
        ]);

        return redirect("/project/{$project->id}")
            ->with('success', 'Laporan harian berhasil ditambahkan. (' . $uploadedCount . ' foto diupload)');
    }

    public function show($projectId, $reportId)
    {
        $project = Project::findOrFail($projectId);
        $report = DailyReport::with(['images', 'creator'])->findOrFail($reportId);

        return view('daily-reports.show', compact('project', 'report'));
    }

    public function showDetail($projectId, $reportId)
    {
        return $this->show($projectId, $reportId);
    }

    public function edit($projectId, $reportId)
    {
        $project = Project::findOrFail($projectId);
        $report = DailyReport::with('images')->findOrFail($reportId);

        return view('daily-reports.edit', compact('project', 'report'));
    }

    public function update(Request $request, $projectId, $reportId)
    {
        $project = Project::findOrFail($projectId);
        $report = DailyReport::findOrFail($reportId);

        $validated = $request->validate([
            'tanggal' => 'required|date',
            'uraian_kegiatan' => 'required|string',
            'cuaca' => 'nullable|string|max:255',
            'jumlah_pekerja' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'captions.*' => 'nullable|string|max:255',
            'existing_images' => 'nullable|array',
            'existing_images.*.id' => 'nullable|exists:daily_report_images,id',
            'existing_images.*.caption' => 'nullable|string|max:255',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'nullable|exists:daily_report_images,id',
        ]);

        $report->update([
            'tanggal' => $validated['tanggal'],
            'uraian_kegiatan' => $validated['uraian_kegiatan'],
            'cuaca' => $validated['cuaca'] ?? null,
            'jumlah_pekerja' => $validated['jumlah_pekerja'] ?? 0,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        // Update existing image captions
        if (!empty($validated['existing_images'])) {
            foreach ($validated['existing_images'] as $imgData) {
                if (isset($imgData['id'])) {
                    $image = DailyReportImage::find($imgData['id']);
                    if ($image) {
                        $image->update(['caption' => $imgData['caption'] ?? null]);
                    }
                }
            }
        }

        // Delete selected images
        if (!empty($validated['delete_images'])) {
            foreach ($validated['delete_images'] as $imageId) {
                $image = DailyReportImage::find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
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

        return redirect("/project/{$project->id}")
            ->with('success', 'Laporan harian berhasil diupdate.');
    }

    public function destroy($projectId, $reportId)
    {
        $project = Project::findOrFail($projectId);
        $report = DailyReport::findOrFail($reportId);

        // Delete all associated images
        foreach ($report->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $report->delete();

        return redirect("/project/{$project->id}")
            ->with('success', 'Laporan harian berhasil dihapus.');
    }

    public function getWorkersCount($projectId, Request $request)
    {
        try {
            $project = Project::findOrFail($projectId);
            $tanggal = $request->input('tanggal');

            $attendance = $project->attendances()
                ->whereDate('tanggal', $tanggal)
                ->first();

            $totalWorkers = 0;
            if ($attendance) {
                $totalWorkers = $attendance->attendanceWorkers()->where('hadir', true)->count();
            }

            return response()->json([
                'total_workers' => $totalWorkers,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'total_workers' => 0,
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadPdf($projectId, $reportId)
    {
        $project = Project::findOrFail($projectId);
        $report = DailyReport::with(['images', 'creator'])->findOrFail($reportId);

        $pdf = PDF::loadView('daily-reports.pdf', compact('project', 'report'));
        
        $filename = 'Laporan_Harian_' . $report->tanggal->format('Y-m-d') . '_' . $project->nama_project . '.pdf';
        
        return $pdf->download($filename);
    }
}
