<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Project extends Model
{
    protected $fillable = [
        'nama_project',
        'nilai_project',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'nilai_project' => 'decimal:2',
    ];

    public function scopeActiveOn(Builder $query, $date)
    {
        return $query->where('tanggal_mulai', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->where('tanggal_selesai', '>=', $date)
                    ->orWhereNull('tanggal_selesai');
            });
    }

    /**
     * Scope untuk project yang aktif pada bulan dan tahun tertentu
     */
    public function scopeActiveInMonth(Builder $query, $year, $month)
    {
        $startDate = date("$year-$month-01");
        $endDate = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
        
        return $query->where('tanggal_mulai', '<=', $endDate)
            ->where(function ($q) use ($startDate) {
                $q->where('tanggal_selesai', '>=', $startDate)
                    ->orWhereNull('tanggal_selesai');
            });
    }

    public function scopeByYear(Builder $query, $year)
    {
        return $query->where(function ($q) use ($year) {
            $q->whereRaw('EXTRACT(YEAR FROM tanggal_mulai) = ?', [$year])
                ->orWhere(function ($sub) use ($year) {
                    $sub->whereNotNull('tanggal_selesai')
                        ->whereRaw('EXTRACT(YEAR FROM tanggal_selesai) = ?', [$year]);
                });
        });
    }

    public function scopeByMonthYear(Builder $query, $year, $month)
    {
        return $query->where(function ($q) use ($year, $month) {
            $q->where(function ($sub) use ($year, $month) {
                $sub->whereRaw('EXTRACT(YEAR FROM tanggal_mulai) = ? AND EXTRACT(MONTH FROM tanggal_mulai) = ?', [$year, $month]);
            })
            ->orWhere(function ($sub) use ($year, $month) {
                $sub->whereRaw('EXTRACT(YEAR FROM tanggal_selesai) = ? AND EXTRACT(MONTH FROM tanggal_selesai) = ?', [$year, $month]);
            })
            ->orWhere(function ($sub) use ($year, $month) {
                $startDate = date("$year-$month-01");
                $endDate = date("Y-m-t", mktime(0, 0, 0, $month, 1, $year));
                $sub->where('tanggal_mulai', '<=', $endDate)
                    ->where('tanggal_selesai', '>=', $startDate);
            });
        });
    }

    public function scopeSearch(Builder $query, $keyword)
    {
        return $query->where('nama_project', 'LIKE', "%{$keyword}%");
    }

    public function scopeWithTasks(Builder $query)
    {
        return $query->with(['tasks' => function ($q) {
            $q->select('id', 'project_id', 'nama_task', 'bobot_rupiah', 'is_done', 'created_at');
        }]);
    }

    public function scopeWithWorkers(Builder $query)
    {
        return $query->with(['workers' => function ($q) {
            $q->select('id', 'project_id', 'nama_worker', 'jabatan', 'aktif');
        }]);
    }

    // Relationships

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function workers()
    {
        return $this->hasMany(ProjectWorker::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function shopDrawings()
    {
        return $this->hasMany(ShopDrawing::class);
    }

    // Methods

    public function totalBobot()
    {
        return $this->nilai_project;
    }

    public function bobotSelesai()
    {
        return $this->tasks()->where('is_done', true)->sum('bobot_rupiah');
    }

    public function progress()
    {
        if ($this->nilai_project == 0) return 0;
        return round(($this->bobotSelesai() / $this->totalBobot()) * 100, 2);
    }
}

