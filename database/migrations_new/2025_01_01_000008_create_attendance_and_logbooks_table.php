<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Absensi Harian
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->decimal('check_in_lat', 10, 7)->nullable(); // GPS latitude
            $table->decimal('check_in_lng', 11, 7)->nullable(); // GPS longitude
            $table->string('check_in_photo')->nullable();       // Selfie check-in
            $table->time('check_out_time')->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 11, 7)->nullable();
            $table->integer('work_duration_minutes')->nullable(); // Durasi kerja
            $table->enum('status', ['present', 'absent', 'permission', 'sick'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['internship_id', 'date']); // Satu absensi per hari per magang
        });

        // Logbook Harian
        Schema::create('logbooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('title'); // Judul kegiatan
            $table->text('description'); // Deskripsi aktivitas
            $table->text('learning_outcomes')->nullable(); // Hasil pembelajaran
            $table->string('attachment')->nullable(); // File lampiran
            $table->enum('status', ['draft', 'submitted', 'reviewed_dpl', 'reviewed_industry'])->default('submitted');
            $table->timestamps();
            $table->softDeletes();
        });

        // Review Logbook (oleh DPL dan Industri)
        Schema::create('logbook_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logbook_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('reviewer_type', ['dpl', 'industry']); // Siapa yang review
            $table->text('comment'); // Komentar review
            $table->enum('status', ['noted', 'revision', 'approved'])->default('noted');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_reviews');
        Schema::dropIfExists('logbooks');
        Schema::dropIfExists('attendances');
    }
};
