<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $order = $request->get('order', 'asc');
        $perPage = $request->get('per_page', 20);
        
        $projects = Project::with(['tasks', 'workers'])
            ->orderBy('nama_project', $order)
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'projects' => $projects->items(),
                'pagination' => [
                    'current_page' => $projects->currentPage(),
                    'last_page' => $projects->lastPage(),
                    'per_page' => $projects->perPage(),
                    'total' => $projects->total(),
                    'has_more' => $projects->hasMorePages(),
                ],
            ],
        ]);
    }

    /**
     * Store a newly created project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nama_project'   => 'required|string|max:255',
            'nilai_project'  => 'required|numeric',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $project = Project::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully',
            'data'    => $project,
        ], 201);
    }

    /**
     * Display the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['tasks', 'workers', 'attendances', 'shopDrawings', 'dailyReports']);

        return response()->json([
            'success' => true,
            'data' => $project,
        ]);
    }

    /**
     * Update the specified project.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'nama_project'   => 'required|string|max:255',
            'nilai_project'  => 'required|numeric',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully',
            'data'    => $project->fresh(),
        ]);
    }

    /**
     * Remove the specified project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        $project->tasks()->delete();
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully',
        ]);
    }
}
