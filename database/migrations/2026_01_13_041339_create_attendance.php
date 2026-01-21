<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->date('tanggal');
            $table->timestamps();

            $table->unique(['project_id','tanggal']);
        });

        Schema::create('attendance_workers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_worker_id')->constrained()->cascadeOnDelete();
            $table->boolean('hadir')->default(false);
            $table->string('keterangan')->nullable();
        });

        Schema::table('attendance_workers', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
