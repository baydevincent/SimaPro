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
     * - Filter berdasarkan tanggal (tanggal_mulai, tanggal_selesai)
     * - Pencarian berdasarkan nama project
     * - Sorting berdasarkan created_at
     * - Composite index untuk query range tanggal
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Index untuk filter dan sorting tanggal
            $table->index('tanggal_mulai', 'idx_projects_tanggal_mulai');
            $table->index('tanggal_selesai', 'idx_projects_tanggal_selesai');
            
            // Index untuk pencarian nama project
            $table->index('nama_project', 'idx_projects_nama_project');
            
            // Index untuk sorting dan filtering created_at
            $table->index('created_at', 'idx_projects_created_at');
            
            // Composite index untuk query range tanggal (query calendar & filter)
            $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_projects_range_tanggal');
            
            // Index untuk status project (aktif/selesai) berdasarkan tanggal
            $table->index(['tanggal_selesai', 'tanggal_mulai'], 'idx_projects_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_tanggal_mulai');
            $table->dropIndex('idx_projects_tanggal_selesai');
            $table->dropIndex('idx_projects_nama_project');
            $table->dropIndex('idx_projects_created_at');
            $table->dropIndex('idx_projects_range_tanggal');
            $table->dropIndex('idx_projects_status');
        });
    }
};
