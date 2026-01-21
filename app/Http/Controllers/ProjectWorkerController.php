<?php

namespace App\Http\Controllers;

use App\Models\Worker;
use App\Models\Project;
use App\Models\ProjectWorker;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectWorkerController extends Controller
{
    public function index(Project $project)
    {
        $workers = ProjectWorker::latest()->get();
        return view('worker.index', compact('workers'));
    }


    public function store(Request $request, Project $project)
    {
        $request->validate([
            'nama_worker' => 'required|string|max:255',
            'jabatan'     => 'nullable|string|max:255',
        ]);

        $worker = $project->workers()->create([
            'nama_worker' => $request->nama_worker,
            'jabatan'     => $request->jabatan,
            'aktif'       => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Worker berhasil ditambahkan',
            'data'    => $worker
        ]);
    }


    public function toggle(Task $task)
    {
        $task->update(['is_done' => !$task->is_done]);
        return back();
    }

    public function destroy($project, $worker)
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project)
            ->firstOrFail();

        $workerModel->delete();

        return response()->json([
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }
}
