<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'project_id',
        'tanggal'
    ];

    public function workers()
    {
        return $this->hasMany(AttendanceWorker::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function attendanceWorkers()
    {
        return $this->hasMany(AttendanceWorker::class);
    }
}