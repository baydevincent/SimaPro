<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worker extends Model
{
    protected $fillable = [
        'nama_worker',
        'jabatan',
        'no_hp',
        'aktif'
    ];

    public function attendances()
    {
        return $this->hasMany(WorkerAttendance::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_workers');
    }


}

