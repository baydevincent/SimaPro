<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Project;
use App\Models\AttendanceWorker;
use App\Models\ProjectWorker;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendances for a project.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Project $project): JsonResponse
    {
        $attendances = Attendance::where('project_id', $project->id)
            ->with(['attendanceWorkers.projectWorker'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Store a newly created attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'tanggal' => 'required|date',
            'kehadiran' => 'array',
        ]);

        $attendance = Attendance::firstOrCreate(
            [
                'project_id' => $project->id,
                'tanggal' => $validated['tanggal'],
            ]
        );

        $projectWorkers = $project->workers;

        foreach ($projectWorkers as $pw) {
            $kehadiranData = $request->input('kehadiran.' . $pw->id, []);

            AttendanceWorker::updateOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'project_worker_id' => $pw->id,
                ],
                [
                    'hadir' => !empty($kehadiranData['hadir']),
                    'keterangan' => $kehadiranData['keterangan'] ?? null,
                ]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance saved successfully',
            'data' => $attendance->fresh(['attendanceWorkers.projectWorker']),
        ], 201);
    }

    /**
     * Display the specified attendance.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $attendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Project $project, $attendance): JsonResponse
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project->id)
            ->with(['attendanceWorkers.projectWorker'])
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $attendanceModel,
        ]);
    }

    /**
     * Update the specified attendance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @param  int  $attendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Project $project, $attendance): JsonResponse
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project->id)
            ->with('attendanceWorkers')
            ->firstOrFail();

        $validated = $request->validate([
            'kehadiran' => 'array',
        ]);

        foreach ($attendanceModel->attendanceWorkers as $aw) {
            $kehadiranData = $request->input('kehadiran.' . $aw->id, []);

            $aw->update([
                'hadir' => !empty($kehadiranData['hadir']),
                'keterangan' => $kehadiranData['keterangan'] ?? null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => $attendanceModel->fresh(['attendanceWorkers.projectWorker']),
        ]);
    }

    /**
     * Remove the specified attendance.
     *
     * @param  \App\Models\Project  $project
     * @param  int  $attendance
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Project $project, $attendance): JsonResponse
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project->id)
            ->firstOrFail();

        $attendanceModel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance deleted successfully',
        ]);
    }
}
