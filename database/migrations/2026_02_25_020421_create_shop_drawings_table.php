<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Tabel untuk menyimpan shop drawings per project
     */
    public function up(): void
    {
        Schema::create('shop_drawings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->string('nama_file');
            $table->string('nama_file_asli');
            $table->string('file_path');
            $table->string('file_mime_type');
            $table->unsignedBigInteger('file_size'); // dalam bytes
            $table->string('deskripsi')->nullable();
            $table->string('uploaded_by')->nullable(); // nama user yang upload
            $table->timestamps();

            // Index untuk performa
            $table->index('project_id', 'idx_shop_drawings_project_id');
            $table->index('created_at', 'idx_shop_drawings_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_drawings');
    }
};
