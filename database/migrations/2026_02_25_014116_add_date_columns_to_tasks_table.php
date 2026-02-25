<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom tanggal_mulai dan tanggal_selesai ke tabel tasks
     * untuk mendukung fitur calendar dan timeline task.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->date('tanggal_mulai')->nullable()->after('nama_task');
            $table->date('tanggal_selesai')->nullable()->after('tanggal_mulai');
            
            // Index untuk kolom tanggal
            $table->index('tanggal_mulai', 'idx_tasks_tanggal_mulai');
            $table->index('tanggal_selesai', 'idx_tasks_tanggal_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_tanggal_mulai');
            $table->dropIndex('idx_tasks_tanggal_selesai');
            $table->dropColumn(['tanggal_mulai', 'tanggal_selesai']);
        });
    }
};
