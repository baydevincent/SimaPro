<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Worker;
use App\Models\ProjectWorker;
use App\Models\WorkerAttendance;
use Illuminate\Http\Request;

class WorkerAttendanceController extends Controller
{
    public function index(Project $project)
    {
        $tanggal = now()->toDateString();

        $workers = ProjectWorker::where('aktif', true)->get();

        $attendances = WorkerAttendance::where('project_id', $project->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('worker_id');

        return view('absen.absen', compact(
            'project', 'workers', 'attendances', 'tanggal'
        ));
    }

    public function toggle(Request $request)
    {
        $attendance = WorkerAttendance::updateOrCreate(
            [
                'project_id' => $request->project_id,
                'worker_id'  => $request->worker_id,
                'tanggal'    => $request->tanggal
            ],
            [
                'hadir' => $request->hadir
            ]
        );

        return response()->json([
            'status' => true,
            'hadir'  => $attendance->hadir
        ]);
    }
}

