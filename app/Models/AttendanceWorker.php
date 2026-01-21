<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceWorker extends Model
{
    protected $fillable = [
        'attendance_id',
        'project_worker_id',
        'hadir',
        'keterangan'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function projectWorker()
    {
        return $this->belongsTo(ProjectWorker::class);
    }
}