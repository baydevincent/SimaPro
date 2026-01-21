<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('tasks')->get();
        return view('projectntask.index', compact('projects'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_project'   => 'required|string',
            'nilai_project'  => 'required|numeric',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $project = Project::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Project berhasil disimpan',
            'data'    => $project
        ]);
    }


    public function show(Project $project)
    {
        $project->load(['tasks', 'attendances.attendanceWorkers']);
        return view('projectntask.pdetail', compact('project'));
    }

    public function destroy(Project $project)
    {
        $project->tasks()->delete();

        // if ($project->shopdrawing) {
        //     \Storage::delete($project->shopdrawing);
        // }

        $project->delete();

        return response()->json([
            'message' => 'Project berhasil dihapus'
        ]);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

