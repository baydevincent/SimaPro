<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $projects = Project::with(['tasks', 'workers'])->get();

        // Daftar warna untuk proyek
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

        // Format data untuk kalender
        $events = [];
        foreach ($projects as $index => $project) {
            if ($project->tanggal_mulai && $project->tanggal_selesai) {
                // Pilih warna berdasarkan indeks proyek untuk memastikan konsistensi
                $color = $colors[$index % count($colors)];

                $events[] = [
                    'title' => $project->nama_project,
                    'start' => $project->tanggal_mulai,
                    'end' => $project->tanggal_selesai,
                    'color' => $color, // Warna berbeda untuk setiap proyek
                    'extendedProps' => [
                        'type' => 'project',
                        'id' => $project->id,
                        'nilai_project' => $project->nilai_project,
                        'progress' => $project->progress()
                    ]
                ];
            }

            // Tambahkan juga task sebagai event dengan warna yang berbeda
            foreach ($project->tasks as $taskIndex => $task) {
                if ($task->tanggal_mulai && $task->tanggal_selesai) {
                    // Gunakan warna yang berbeda untuk task, bisa variasikan dari warna proyeknya
                    $taskColor = $this->adjustColorBrightness($colors[$index % count($colors)], 20);

                    $events[] = [
                        'title' => $task->nama_task,
                        'start' => $task->tanggal_mulai,
                        'end' => $task->tanggal_selesai,
                        'color' => $taskColor, // Warna variasi dari warna proyek
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

        return view('calendar.index', compact('events'));
    }

    /**
     * Fungsi untuk mengubah kecerahan warna
     */
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
