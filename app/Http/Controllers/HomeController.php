<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $bulan = $request->input('bulan');

        $query = Project::with('tasks');

        // Filter berdasarkan tahun dan bulan
        if ($tahun) {
            if ($bulan) {
                // Filter untuk project yang aktif dalam bulan dan tahun tertentu
                $query->where(function($q) use ($tahun, $bulan) {
                    // Project yang mulai atau selesai dalam bulan dan tahun tertentu
                    $q->where(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tanggal_mulai', $tahun)
                            ->whereMonth('tanggal_mulai', $bulan);
                    })
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        $sub->whereYear('tanggal_selesai', $tahun)
                            ->whereMonth('tanggal_selesai', $bulan);
                    })
                    ->orWhere(function($sub) use ($tahun, $bulan) {
                        // Project yang sedang berlangsung selama bulan tersebut
                        $startDate = date("$tahun-$bulan-01");
                        $endDate = date("Y-m-t", mktime(0, 0, 0, $bulan, 1, $tahun)); // Akhir bulan
                        $sub->where('tanggal_mulai', '<=', $endDate)
                            ->where('tanggal_selesai', '>=', $startDate);
                    });
                });
            } else {
                // Filter hanya berdasarkan tahun
                $query->where(function($q) use ($tahun) {
                    $q->whereYear('tanggal_mulai', $tahun)
                      ->orWhere(function($sub) use ($tahun) {
                          $sub->whereNotNull('tanggal_selesai')
                              ->whereYear('tanggal_selesai', $tahun);
                      });
                });
            }
        }

        $projects = $query->get();

        // Dapatkan tahun-tahun yang tersedia dari data project
        $allProjects = Project::select('tanggal_mulai', 'tanggal_selesai')->get();

        $years = [];
        foreach ($allProjects as $project) {
            if ($project->tanggal_mulai) {
                $years[] = date('Y', strtotime($project->tanggal_mulai));
            }
            if ($project->tanggal_selesai) {
                $years[] = date('Y', strtotime($project->tanggal_selesai));
            }
        }

        $availableYears = array_values(array_unique($years));
        sort($availableYears);

        return view('home', compact('projects', 'tahun', 'bulan', 'availableYears'));
    }
}
