<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

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

        // Gunakan scopes untuk query yang lebih efisien
        $query = Project::withTasks();

        // Filter berdasarkan tahun dan bulan
        if ($tahun) {
            if ($bulan) {
                // Gunakan scope ByMonthYear untuk query yang lebih optimal
                $query->ByMonthYear($tahun, $bulan);
            } else {
                // Gunakan scope ByYear untuk query yang lebih optimal
                $query->ByYear($tahun);
            }
        }

        $projects = $query->get();

        // Cache available years untuk performa (TTL: 1 jam)
        $availableYears = Cache::remember('available_years', 3600, function () {
            // Gunakan EXTRACT untuk PostgreSQL compatibility
            return Project::selectRaw('EXTRACT(YEAR FROM tanggal_mulai)::integer as year')
                ->union(Project::selectRaw('EXTRACT(YEAR FROM tanggal_selesai)::integer as year'))
                ->distinct()
                ->pluck('year')
                ->sort()
                ->values()
                ->toArray();
        });

        return view('home', compact('projects', 'tahun', 'bulan', 'availableYears'));
    }
}
