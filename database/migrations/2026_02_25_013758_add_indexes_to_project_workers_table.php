<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Index ditambahkan untuk optimasi query pada:
     * - Foreign key project_id
     * - Filter worker aktif
     * - Pencarian worker berdasarkan nama
     * - Composite index untuk worker aktif per project
     */
    public function up(): void
    {
        Schema::table('project_workers', function (Blueprint $table) {
            // Index untuk foreign key (mempercepat JOIN dan WHERE project_id)
            $table->index('project_id', 'idx_project_workers_project_id');
            
            // Index untuk filter worker aktif
            $table->index('aktif', 'idx_project_workers_aktif');
            
            // Index untuk pencarian worker berdasarkan nama
            $table->index('nama_worker', 'idx_project_workers_nama');
            
            // Composite index untuk query worker aktif per project
            $table->index(['project_id', 'aktif'], 'idx_project_workers_project_aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_workers', function (Blueprint $table) {
            $table->dropIndex('idx_project_workers_project_id');
            $table->dropIndex('idx_project_workers_aktif');
            $table->dropIndex('idx_project_workers_nama');
            $table->dropIndex('idx_project_workers_project_aktif');
        });
    }
};
