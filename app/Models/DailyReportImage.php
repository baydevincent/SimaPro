<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_report_id',
        'image_path',
        'caption',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
