<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengajuan Magang Mahasiswa (lamaran ke lowongan spesifik)
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vacancy_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained();

            // Status alur pengajuan
            $table->enum('status', [
                'pending',          // Menunggu validasi Kaprodi
                'kaprodi_approved', // Kaprodi setuju, menunggu seleksi industri
                'kaprodi_rejected', // Kaprodi tolak
                'industry_accepted',// Industri terima
                'industry_rejected',// Industri tolak
                'cancelled'         // Dibatalkan
            ])->default('pending');

            // Kaprodi review
            $table->text('kaprodi_notes')->nullable();
            $table->timestamp('kaprodi_reviewed_at')->nullable();
            $table->foreignId('kaprodi_reviewed_by')->nullable()->constrained('users');

            // Industri review
            $table->text('industry_notes')->nullable();
            $table->timestamp('industry_reviewed_at')->nullable();

            // Dokumen pendukung
            $table->string('cv_file')->nullable();
            $table->string('motivation_letter')->nullable();
            $table->text('cover_letter')->nullable();

            $table->timestamps();
        });

        // Magang Aktif (Setelah diterima industri dan DPL di-assign)
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->foreignId('vacancy_id')->constrained();
            $table->foreignId('academic_period_id')->constrained();

            $table->enum('status', [
                'waiting_dpl', // Menunggu penugasan DPL dari Kaprodi
                'active',      // Magang sedang berjalan
                'completed',   // Magang selesai
                'terminated'   // Magang dibatalkan/dihentikan
            ])->default('waiting_dpl');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_end_date')->nullable();
            $table->text('termination_reason')->nullable();
            $table->timestamps();
        });

        // Penugasan DPL ke Mahasiswa
        Schema::create('dpl_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lecturer_id')->constrained();
            $table->foreignId('assigned_by')->constrained('users'); // Kaprodi
            $table->timestamp('assigned_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpl_assignments');
        Schema::dropIfExists('internships');
        Schema::dropIfExists('applications');
    }
};
