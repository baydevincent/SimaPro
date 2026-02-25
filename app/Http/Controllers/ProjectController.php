<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{
    public function index()
    {
        $order = request()->get('order', 'asc'); // Default to ascending order
        $projects = Project::with('tasks')->orderBy('nama_project', $order)->paginate(20);
        return view('projectntask.index', compact('projects', 'order'));
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
        $project->load(['tasks', 'workers', 'attendances.attendanceWorkers', 'shopDrawings']);
        $shopDrawings = $project->shopDrawings()->orderBy('created_at', 'desc')->get();
        return view('projectntask.pdetail', compact('project', 'shopDrawings'));
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

    public function edit(Project $project)
    {
        return view('projectntask.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        // Check authorization - only allow authenticated users to update projects
        if (!auth()->check()) {
            return redirect()->route('project')->with('error', 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'nama_project'   => 'required|string',
            'nilai_project'  => 'required|numeric',
            'tanggal_mulai'  => 'required|date',
            'tanggal_selesai'=> 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $project->update([
            'nama_project' => $request->nama_project,
            'nilai_project' => $request->nilai_project,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
        ]);

        return redirect()->route('project')->with('success', 'Project berhasil diperbarui');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}

