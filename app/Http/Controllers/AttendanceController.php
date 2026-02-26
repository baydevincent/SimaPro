<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Project;
use App\Models\AttendanceWorker;
use App\Models\ProjectWorker;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Project $project)
    {
        $attendances = Attendance::where('project_id', $project->id)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('attendance.index', compact('project', 'attendances'));
    }

    public function create(Project $project)
    {

        $tanggal = request()->get('tanggal', now()->toDateString());

        $projectWorkers = ProjectWorker::where('project_id', $project->id)->get();

        $existingAttendance = [];
        $attendance = Attendance::where('project_id', $project->id)
            ->where('tanggal', $tanggal)
            ->first();

        if ($attendance) {
            $existingAttendance = $attendance->attendanceWorkers->pluck('keterangan', 'project_worker_id')->toArray();
        }

        return view('attendance.create', compact('project', 'projectWorkers', 'tanggal', 'existingAttendance'));
    }

    public function store(Request $request, Project $project)
    {
        try {
            $request->validate([
                'tanggal' => 'required|date',
                'kehadiran' => 'array'
            ]);

            $attendance = Attendance::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'tanggal' => $request->tanggal
                ]
            );

            $projectWorkers = $project->workers;

            foreach ($projectWorkers as $pw) {
                $kehadiranData = $request->input('kehadiran.' . $pw->id, []);

                AttendanceWorker::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'project_worker_id' => $pw->id
                    ],
                    [
                        'hadir' => !empty($kehadiranData['hadir']),
                        'keterangan' => $kehadiranData['keterangan'] ?? null
                    ]
                );
            }

            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Absensi berhasil disimpan.'
                ]);
            }

            return redirect()->route('attendance.index', $project)->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan absensi.']);
        }
    }

    public function show($project, $attendance)
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project)
            ->with(['attendanceWorkers.projectWorker', 'project'])
            ->firstOrFail();

        return view('attendance.show', compact('attendanceModel'))->with('attendance', $attendanceModel);
    }

    public function edit($project, $attendance)
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project)
            ->with('attendanceWorkers')
            ->firstOrFail();

        return view('attendance.edit', compact('attendanceModel'))->with('attendance', $attendanceModel);
    }

    public function update(Request $request, $project, $attendance)
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project)
            ->with('attendanceWorkers')
            ->firstOrFail();

        $request->validate([
            'kehadiran' => 'array'
        ]);

        // Update berdasarkan attendance_workers yang sudah ada
        foreach ($attendanceModel->attendanceWorkers as $aw) {
            $kehadiranData = $request->input('kehadiran.' . $aw->id, []);

            $aw->update([
                'hadir' => !empty($kehadiranData['hadir']),
                'keterangan' => $kehadiranData['keterangan'] ?? null
            ]);
        }

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
            return response()->json([
                'success' => true,
                'message' => 'Absensi berhasil diperbarui.'
            ]);
        }

        return redirect()->route('attendance.index', $attendanceModel->project)->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy($project, $attendance)
    {
        try {
            $attendanceModel = Attendance::where('id', $attendance)
                ->where('project_id', $project)
                ->with('project')
                ->firstOrFail();

            $tanggal = $attendanceModel->tanggal;

            $attendanceModel->delete();

            return response()->json([
                'success' => true,
                'message' => 'Absensi tanggal ' . \Carbon\Carbon::parse($tanggal)->format('d M Y') . ' berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus absensi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAttendanceForDate(Request $request, Project $project)
    {
        $tanggal = $request->tanggal;

        $attendance = Attendance::where('project_id', $project->id)
            ->where('tanggal', $tanggal)
            ->with(['attendanceWorkers' => function($query) {
                $query->with('projectWorker');
            }])
            ->first();

        if (!$attendance) {
            $projectWorkers = ProjectWorker::where('project_id', $project->id)->get();
            $attendanceData = [];
            
            foreach ($projectWorkers as $pw) {
                $attendanceData[] = [
                    'project_worker' => $pw,
                    'hadir' => null,
                    'keterangan' => null
                ];
            }
            
            return response()->json([
                'tanggal' => $tanggal,
                'attendance_data' => $attendanceData
            ]);
        }

        return response()->json([
            'tanggal' => $tanggal,
            'attendance_data' => $attendance->attendanceWorkers
        ]);
    }

    public function getAttendanceDates(Project $project)
    {
        $attendances = Attendance::where('project_id', $project->id)
            ->select('id', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->get();

        return response()->json([
            'dates' => $attendances->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'tanggal' => $attendance->tanggal,
                    'tanggal_formatted' => \Carbon\Carbon::parse($attendance->tanggal)->format('d M Y')
                ];
            })
        ]);
    }
}