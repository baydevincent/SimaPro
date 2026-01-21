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

        // Dapatkan data kehadiran yang sudah ada untuk tanggal ini
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

            // Cek apakah sudah ada absensi untuk tanggal ini
            $attendance = Attendance::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'tanggal' => $request->tanggal
                ]
            );

            // Ambil semua pekerja dalam proyek ini
            $projectWorkers = $project->workers;

            // Simpan data kehadiran untuk setiap pekerja
            foreach ($projectWorkers as $pw) {
                $kehadiranData = $request->input('kehadiran.' . $pw->id, []);

                AttendanceWorker::updateOrCreate(
                    [
                        'attendance_id' => $attendance->id,
                        'project_worker_id' => $pw->id
                    ],
                    [
                        'hadir' => !empty($kehadiranData['hadir']), // true jika checkbox dicentang, false jika tidak
                        'keterangan' => $kehadiranData['keterangan'] ?? null
                    ]
                );
            }

            // Check if request is AJAX by checking for X-Requested-With header or wantsJson
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Absensi berhasil disimpan.'
                ]);
            }

            return redirect()->route('attendance.index', $project)->with('success', 'Absensi berhasil disimpan.');
        } catch (\Exception $e) {
            // If it's an AJAX request, return JSON error
            if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menyimpan absensi: ' . $e->getMessage()
                ], 500);
            }

            // For non-AJAX requests, redirect back with error
            return redirect()->back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan absensi.']);
        }
    }

    public function show($project, $attendance)
    {
        // Ambil attendance berdasarkan ID dan pastikan terkait dengan project yang benar
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

        // Ambil semua pekerja dalam proyek ini
        $projectWorkers = $attendanceModel->project->workers;

        // Update data kehadiran untuk setiap pekerja
        foreach ($projectWorkers as $pw) {
            $kehadiranData = $request->input('kehadiran.' . $pw->id, []);

            // Update atau buat data kehadiran untuk setiap pekerja
            AttendanceWorker::updateOrCreate(
                [
                    'attendance_id' => $attendanceModel->id,
                    'project_worker_id' => $pw->id
                ],
                [
                    'hadir' => !empty($kehadiranData['hadir']), // true jika checkbox dicentang, false jika tidak
                    'keterangan' => $kehadiranData['keterangan'] ?? null
                ]
            );
        }

        return redirect()->route('attendance.index', $attendanceModel->project)->with('success', 'Absensi berhasil diperbarui.');
    }

    public function destroy($project, $attendance)
    {
        $attendanceModel = Attendance::where('id', $attendance)
            ->where('project_id', $project)
            ->firstOrFail();

        $projectModel = $attendanceModel->project;
        $attendanceModel->delete();

        return redirect()->route('attendance.index', $projectModel)->with('success', 'Absensi berhasil dihapus.');
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