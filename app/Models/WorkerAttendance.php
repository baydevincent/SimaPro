<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerAttendance extends Model
{
    protected $fillable = [
        'project_id',
        'worker_id',
        'tanggal',
        'hadir'
    ];

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}


