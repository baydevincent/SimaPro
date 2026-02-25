<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopDrawing extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'nama_file',
        'nama_file_asli',
        'file_path',
        'file_mime_type',
        'file_size',
        'deskripsi',
        'uploaded_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Relasi ke Project
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Scope untuk filter by project
     */
    public function scopeByProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope untuk pencarian
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use ($keyword) {
            $q->where('nama_file_asli', 'LIKE', "%{$keyword}%")
              ->orWhere('deskripsi', 'LIKE', "%{$keyword}%");
        });
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedFileSizeAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get full URL to file
     */
    public function getFileUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

    /**
     * Check if file is image
     */
    public function getIsImageAttribute()
    {
        $imageTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        return in_array($this->file_mime_type, $imageTypes);
    }

    /**
     * Check if file is PDF
     */
    public function getIsPdfAttribute()
    {
        return $this->file_mime_type === 'application/pdf';
    }
}
