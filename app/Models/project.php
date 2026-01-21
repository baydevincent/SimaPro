<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'nama_project',
        'nilai_project',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

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

    public function workers()
    {
        return $this->hasMany(ProjectWorker::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }


}

