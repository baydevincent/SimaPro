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
            'posisi'     => 'nullable|string|max:255',
        ]);

        $worker = $project->workers()->create([
            'nama_worker' => $request->nama_worker,
            'posisi'     => $request->posisi,
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

    public function edit($project, $worker)
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $workerModel
        ]);
    }

    public function update(Request $request, $project, $worker)
    {
        $workerModel = ProjectWorker::where('id', $worker)
            ->where('project_id', $project)
            ->firstOrFail();

        $request->validate([
            'nama_worker' => 'required|string|max:255',
            'posisi'      => 'nullable|string|max:255',
            'no_hp'       => 'nullable|string|max:20',
            'aktif'       => 'nullable|boolean',
        ]);

        $workerModel->update([
            'nama_worker' => $request->nama_worker,
            'posisi'      => $request->posisi,
            'no_hp'       => $request->no_hp,
            'aktif'       => $request->aktif ? true : false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Data worker berhasil diperbarui',
            'data'    => $workerModel
        ]);
    }
}
