<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laporan Akhir Magang
        Schema::create('final_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Judul laporan
            $table->string('file_path'); // Path file laporan
            $table->integer('file_size')->nullable(); // Ukuran file (bytes)
            $table->enum('status', ['submitted', 'reviewed', 'approved', 'revision'])->default('submitted');
            $table->text('dpl_feedback')->nullable(); // Feedback DPL
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Penilaian (oleh DPL dan Industri)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessor_id')->constrained('users')->cascadeOnDelete();
            $table->enum('assessor_type', ['dpl', 'industry']); // Siapa yang menilai

            // Komponen Penilaian DPL
            $table->decimal('report_score', 5, 2)->nullable();      // Nilai laporan
            $table->decimal('presentation_score', 5, 2)->nullable(); // Nilai presentasi
            $table->decimal('logbook_score', 5, 2)->nullable();      // Nilai logbook

            // Komponen Penilaian Industri
            $table->decimal('discipline_score', 5, 2)->nullable();   // Kedisiplinan
            $table->decimal('skill_score', 5, 2)->nullable();        // Kompetensi teknis
            $table->decimal('attitude_score', 5, 2)->nullable();     // Sikap & etika
            $table->decimal('teamwork_score', 5, 2)->nullable();     // Kerjasama tim
            $table->decimal('initiative_score', 5, 2)->nullable();   // Inisiatif

            $table->decimal('final_score', 5, 2)->nullable(); // Nilai akhir (auto-kalkulasi)
            $table->text('feedback')->nullable();
            $table->timestamp('assessed_at')->nullable();
            $table->timestamps();

            $table->unique(['internship_id', 'assessor_type']); // Satu penilaian per tipe per magang
        });

        // Konversi Nilai ke SKS oleh BAAK
        Schema::create('grade_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->decimal('industry_score', 5, 2)->nullable(); // Nilai dari industri (bobot 40%)
            $table->decimal('dpl_score', 5, 2)->nullable();      // Nilai dari DPL (bobot 60%)
            $table->decimal('final_score', 5, 2)->nullable();    // Nilai akhir gabungan
            $table->string('letter_grade', 5)->nullable();       // A, A-, B+, B, dll
            $table->decimal('grade_point', 4, 2)->nullable();    // 4.00, 3.75, dll
            $table->integer('sks_converted')->nullable();        // Jumlah SKS yang dikonversi
            $table->string('mata_kuliah_pengganti')->nullable(); // Nama MK pengganti
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete(); // BAAK officer
            $table->timestamp('processed_at')->nullable();
            $table->enum('status', ['pending', 'processed', 'finalized'])->default('pending');
            $table->timestamps();
        });

        // Sertifikat Digital
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_number')->unique(); // Nomor sertifikat unik
            $table->string('file_path')->nullable(); // Path PDF
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('grade_conversions');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('final_reports');
    }
};
