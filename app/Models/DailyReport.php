<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'created_by',
        'tanggal',
        'uraian_kegiatan',
        'cuaca',
        'jumlah_pekerja',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_pekerja' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function images()
    {
        return $this->hasMany(DailyReportImage::class);
    }
}
