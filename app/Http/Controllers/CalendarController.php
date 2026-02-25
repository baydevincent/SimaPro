<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CalendarController extends Controller
{
    public function index()
    {
        // Cache events untuk 5 menit untuk mengurangi query database
        $events = Cache::remember('calendar_events', 300, function () {
            // Hanya ambil data yang diperlukan dengan eager loading yang optimal
            $projects = Project::select('id', 'nama_project', 'tanggal_mulai', 'tanggal_selesai', 'nilai_project')
                ->with(['tasks' => function($q) {
                    $q->select('id', 'project_id', 'nama_task', 'tanggal_mulai', 'tanggal_selesai');
                }])
                ->whereNotNull('tanggal_mulai')
                ->whereNotNull('tanggal_selesai')
                ->get();

            // Daftar warna
            $colors = [
                '#4e73df', // Blue
                '#1cc88a', // Green
                '#36b9cc', // Cyan
                '#f6c23e', // Yellow
                '#e74a3b', // Red
                '#858796', // Gray
                '#5a5c69', // Dark Gray
                '#6777ef', // Indigo
                '#f55c7a', // Pink
                '#fd7e14', // Orange
                '#6f42c1', // Purple
                '#20c997', // Teal
                '#e83e8c', // Hot Pink
                '#2ea8e5', // Light Blue
                '#abb623', // Olive
            ];

            $events = [];
            foreach ($projects as $index => $project) {
                if ($project->tanggal_mulai && $project->tanggal_selesai) {
                    $color = $colors[$index % count($colors)];

                    $events[] = [
                        'title' => $project->nama_project,
                        'start' => $project->tanggal_mulai->format('Y-m-d'),
                        'end' => $project->tanggal_selesai->format('Y-m-d'),
                        'color' => $color,
                        'extendedProps' => [
                            'type' => 'project',
                            'id' => $project->id,
                            'nilai_project' => $project->nilai_project,
                            'progress' => $project->progress()
                        ]
                    ];
                }

                foreach ($project->tasks as $taskIndex => $task) {
                    if ($task->tanggal_mulai && $task->tanggal_selesai) {
                        $taskColor = $this->adjustColorBrightness($colors[$index % count($colors)], 20);

                        $events[] = [
                            'title' => $task->nama_task,
                            'start' => $task->tanggal_mulai->format('Y-m-d'),
                            'end' => $task->tanggal_selesai->format('Y-m-d'),
                            'color' => $taskColor,
                            'extendedProps' => [
                                'type' => 'task',
                                'id' => $task->id,
                                'project_id' => $project->id,
                                'project_name' => $project->nama_project
                            ]
                        ];
                    }
                }
            }

            return $events;
        });

        return view('calendar.index', compact('events'));
    }

    private function adjustColorBrightness($hexColor, $brightnessChange) {
        // Hilangkan # jika ada
        $hexColor = ltrim($hexColor, '#');

        // Ekstrak RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));

        // Adjust brightness
        $r = max(0, min(255, $r + $brightnessChange));
        $g = max(0, min(255, $g + $brightnessChange));
        $b = max(0, min(255, $b + $brightnessChange));

        // Kembalikan ke format hex
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) .
                     str_pad(dechex($g), 2, '0', STR_PAD_LEFT) .
                     str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
