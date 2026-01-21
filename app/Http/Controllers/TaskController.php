<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validatedData = $request->validate([
            'nama_task' => 'required|string',
        ]);

        // Hanya administrator yang bisa mengatur bobot rupiah
        if (auth()->user()->hasRole('administrator')) {
            $validatedData['bobot_rupiah'] = $request->validate([
                'bobot_rupiah' => 'required|numeric|min:1'
            ])['bobot_rupiah'];
        } else {
            // Untuk mandor, bobot rupiah diatur ke 0
            $validatedData['bobot_rupiah'] = 0;
        }

        $task = $project->tasks()->create($validatedData);

        return response()->json([
            'message' => 'Task berhasil ditambahkan',
            'data' => $task
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $validatedData = $request->validate([
            'nama_task' => 'required|string',
        ]);

        // Hanya administrator yang bisa mengatur bobot rupiah
        if (auth()->user()->hasRole('administrator')) {
            $validatedData['bobot_rupiah'] = $request->validate([
                'bobot_rupiah' => 'required|numeric|min:1'
            ])['bobot_rupiah'];
        } else {
            // Untuk mandor, bobot rupiah tetap sama atau diatur ke 0 jika sebelumnya 0
            $validatedData['bobot_rupiah'] = $task->bobot_rupiah; // Biarkan nilai tetap
        }

        $task->update($validatedData);

        return response()->json([
            'message' => 'Task berhasil diperbarui',
            'data' => $task
        ]);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([
            'message' => 'Task berhasil dihapus'
        ]);
    }

    public function toggle(Task $task)
    {
        $task->update(['is_done' => !$task->is_done]);
        return back();
    }
}

