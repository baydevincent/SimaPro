<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks for a project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $tasks = $project->tasks()->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $tasks,
        ]);
    }

    /**
     * Store a newly created task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'nama_task' => 'required|string|max:255',
            'bobot_rupiah' => 'nullable|numeric|min:0',
        ]);

        // Only administrators can set bobot_rupiah
        $user = auth('api')->user();
        if ($user->hasRole('administrator')) {
            $validated['bobot_rupiah'] = $validated['bobot_rupiah'] ?? 0;
        } else {
            $validated['bobot_rupiah'] = 0;
        }

        $task = $project->tasks()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $task,
        ], 201);
    }

    /**
     * Display the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $task,
        ]);
    }

    /**
     * Update the specified task.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'nama_task' => 'required|string|max:255',
            'bobot_rupiah' => 'nullable|numeric|min:0',
        ]);

        $user = auth('api')->user();
        if ($user->hasRole('administrator')) {
            $validated['bobot_rupiah'] = $validated['bobot_rupiah'] ?? $task->bobot_rupiah;
        } else {
            $validated['bobot_rupiah'] = $task->bobot_rupiah;
        }

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => $task->fresh(),
        ]);
    }

    /**
     * Remove the specified task.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
        ]);
    }

    /**
     * Toggle task completion status.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Task $task): JsonResponse
    {
        $task->update(['is_done' => !$task->is_done]);

        return response()->json([
            'success' => true,
            'message' => 'Task status toggled successfully',
            'data' => $task->fresh(),
        ]);
    }
}
