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
     * - Foreign key project_id (sudah ada constraint, tambah index untuk performa)
     * - Filter berdasarkan status is_done
     * - Sorting berdasarkan created_at
     * - Composite index untuk query task per project
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Index untuk foreign key (mempercepat JOIN dan WHERE project_id)
            $table->index('project_id', 'idx_tasks_project_id');
            
            // Index untuk filter status task (done/ongoing)
            $table->index('is_done', 'idx_tasks_is_done');
            
            // Index untuk sorting dan filtering
            $table->index('created_at', 'idx_tasks_created_at');
            
            // Composite index untuk query task per project dengan status
            $table->index(['project_id', 'is_done'], 'idx_tasks_project_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_project_id');
            $table->dropIndex('idx_tasks_is_done');
            $table->dropIndex('idx_tasks_created_at');
            $table->dropIndex('idx_tasks_project_status');
        });
    }
};
