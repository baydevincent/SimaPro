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
     * - Filter berdasarkan tanggal
     * - Unique index sudah ada untuk (project_id, tanggal)
     * - Index untuk attendance_workers
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Index untuk foreign key (mempercepat JOIN dan WHERE project_id)
            $table->index('project_id', 'idx_attendances_project_id');
            
            // Index untuk filter berdasarkan tanggal
            $table->index('tanggal', 'idx_attendances_tanggal');
            
            // Composite index untuk query attendance per project per tanggal
            $table->index(['project_id', 'tanggal'], 'idx_attendances_project_tanggal');
        });

        Schema::table('attendance_workers', function (Blueprint $table) {
            // Index untuk foreign keys
            $table->index('attendance_id', 'idx_attendance_workers_attendance_id');
            $table->index('project_worker_id', 'idx_attendance_workers_worker_id');
            
            // Index untuk filter kehadiran
            $table->index('hadir', 'idx_attendance_workers_hadir');
            
            // Composite index untuk query kehadiran per attendance
            $table->index(['attendance_id', 'hadir'], 'idx_attendance_workers_attendance_hadir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_project_id');
            $table->dropIndex('idx_attendances_tanggal');
            $table->dropIndex('idx_attendances_project_tanggal');
        });

        Schema::table('attendance_workers', function (Blueprint $table) {
            $table->dropIndex('idx_attendance_workers_attendance_id');
            $table->dropIndex('idx_attendance_workers_worker_id');
            $table->dropIndex('idx_attendance_workers_hadir');
            $table->dropIndex('idx_attendance_workers_attendance_hadir');
        });
    }
};
