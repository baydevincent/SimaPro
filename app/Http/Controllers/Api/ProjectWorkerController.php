<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectWorker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectWorkerController extends Controller
{
    /**
     * Display a listing of workers for a project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $workers = $project->workers()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $workers,
        ]);
    }

    /**
     * Store a newly created worker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'nama_worker' => 'required|string|max:255',
            'posisi'      => 'nullable|string|max:255',
            'no_hp'       => 'nullable|string|max:20',
        ]);

        $worker = $project->workers()->create([
            'nama_worker' => $validated['nama_worker'],
            'posisi'      => $validated['posisi'] ?? null,
            'no_hp'       => $validated['no_hp'] ?? null,
            'aktif'       => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Worker added successfully',
            'data'    => $worker,
        ], 201);
    }

    /**
     * Display the specified worker.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $worker
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project, $worker): JsonResponse
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $workerModel,
        ]);
    }

    /**
     * Update the specified worker.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @param  int  $worker
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Project $project, $worker): JsonResponse
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project->id)
            ->firstOrFail();

        $validated = $request->validate([
            'nama_worker' => 'required|string|max:255',
            'posisi'      => 'nullable|string|max:255',
            'no_hp'       => 'nullable|string|max:20',
            'aktif'       => 'nullable|boolean',
        ]);

        $workerModel->update([
            'nama_worker' => $validated['nama_worker'],
            'posisi'      => $validated['posisi'] ?? $workerModel->posisi,
            'no_hp'       => $validated['no_hp'] ?? $workerModel->no_hp,
            'aktif'       => $validated['aktif'] ?? $workerModel->aktif,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Worker updated successfully',
            'data'    => $workerModel->fresh(),
        ]);
    }

    /**
     * Remove the specified worker.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $worker
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project, $worker): JsonResponse
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project->id)
            ->firstOrFail();

        $workerModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Worker deleted successfully',
        ]);
    }
}
