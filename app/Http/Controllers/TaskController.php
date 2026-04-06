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
        // Mandor tidak bisa membuat task
        if (auth()->user()->hasRole('mandor')) {
            return response()->json([
                'message' => 'Akses ditolak. Mandor tidak dapat menambah task.'
            ], 403);
        }

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

    public function edit(Task $task)
    {
        return view('task.edit', compact('task'));
    }

    public function update(Request $request, Task $task)
    {
        // Mandor tidak bisa mengupdate task
        if (auth()->user()->hasRole('mandor')) {
            return redirect()->route('project.show', $task->project->id)
                ->with('error', 'Akses ditolak. Mandor tidak dapat mengupdate task.');
        }

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

        return redirect()->route('project.show', $task->project->id)->with('success', 'Task berhasil diperbarui');
    }

    public function destroy(Task $task)
    {
        // Mandor tidak bisa menghapus task
        if (auth()->user()->hasRole('mandor')) {
            return response()->json([
                'message' => 'Akses ditolak. Mandor tidak dapat menghapus task.'
            ], 403);
        }

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

