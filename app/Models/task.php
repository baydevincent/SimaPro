<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'nama_task',
        'bobot_rupiah',
        'is_done',
        'tanggal_mulai',
        'tanggal_selesai'
    ];

    protected $casts = [
        'is_done' => 'boolean',
        'bobot_rupiah' => 'decimal:2',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Scopes untuk query yang lebih efisien

    /**
     * Scope untuk task yang sudah selesai
     */
    public function scopeDone(Builder $query)
    {
        return $query->where('is_done', true);
    }

    /**
     * Scope untuk task yang belum selesai
     */
    public function scopePending(Builder $query)
    {
        return $query->where('is_done', false);
    }

    /**
     * Scope untuk task dari project tertentu
     */
    public function scopeByProject(Builder $query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

    /**
     * Scope untuk pencarian task
     */
    public function scopeSearch(Builder $query, $keyword)
    {
        return $query->where('nama_task', 'LIKE', "%{$keyword}%");
    }

    // Relationships

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

