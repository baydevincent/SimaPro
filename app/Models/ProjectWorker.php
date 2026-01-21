<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectWorker extends Model
{
    protected $fillable = [
        'project_id',
        'nama_worker',
        'jabatan',
        'no_hp',
        'aktif'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class);
    }

}
