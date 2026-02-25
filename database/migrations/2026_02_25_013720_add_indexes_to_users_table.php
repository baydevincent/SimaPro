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
     * - Login berdasarkan username
     * - Pencarian user berdasarkan name
     * - Sorting berdasarkan created_at
     * 
     * Catatan: Kolom role tidak ada karena menggunakan sistem role terpisah (role_user table)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Index untuk login berdasarkan username
            $table->index('username', 'idx_users_username');
            
            // Index untuk pencarian user berdasarkan name
            $table->index('name', 'idx_users_name');
            
            // Index untuk sorting dan filtering
            $table->index('created_at', 'idx_users_created_at');
            
            // Index untuk email (sudah unique, tapi tambah index untuk performa)
            $table->index('email', 'idx_users_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_username');
            $table->dropIndex('idx_users_name');
            $table->dropIndex('idx_users_created_at');
            $table->dropIndex('idx_users_email');
        });
    }
};
