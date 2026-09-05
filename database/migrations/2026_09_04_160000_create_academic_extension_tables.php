<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Pengajuan Magang Mandiri (Self-Proposed Internship)
        Schema::create('self_proposed_internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_period_id')->nullable()->constrained('academic_periods')->noActionOnDelete();
            $table->string('company_name');
            $table->string('industry_sector')->nullable();
            $table->text('company_address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->integer('geofence_radius')->default(500);
            $table->string('contact_person_name');
            $table->string('contact_person_position')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->string('contact_person_phone')->nullable();
            $table->string('position_title');
            $table->text('job_description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('loa_file_path');
            $table->string('status')->default('submitted'); // submitted, under_review, revision, approved, rejected
            $table->text('kaprodi_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->noActionOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->foreignId('internship_id')->nullable()->constrained('internships')->noActionOnDelete();
            $table->timestamps();
        });

        // 2. Pendaftaran & Penjadwalan Sidang Magang (Internship Defenses)
        Schema::create('internship_defenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->noActionOnDelete();
            $table->string('presentation_file_path')->nullable();
            $table->string('clearance_file_path')->nullable();
            $table->string('status')->default('registered'); // registered, scheduled, completed, passed, revision, failed
            $table->date('scheduled_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room_or_link')->nullable();
            $table->foreignId('examiner_lecturer_id')->nullable()->constrained('lecturers')->noActionOnDelete();
            $table->foreignId('supervisor_lecturer_id')->nullable()->constrained('lecturers')->noActionOnDelete();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('grade_letter', 5)->nullable();
            $table->text('revision_notes')->nullable();
            $table->date('revision_deadline')->nullable();
            $table->dateTime('passed_at')->nullable();
            $table->string('official_report_number')->nullable();
            $table->timestamps();
        });

        // 3. Rubrik Penilaian Penguji Sidang (Defense Scores)
        Schema::create('internship_defense_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('defense_id')->constrained('internship_defenses')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->noActionOnDelete();
            $table->string('evaluator_role')->default('examiner'); // 'examiner' or 'supervisor'
            $table->decimal('presentation_score', 5, 2)->default(0);
            $table->decimal('material_mastery_score', 5, 2)->default(0);
            $table->decimal('report_quality_score', 5, 2)->default(0);
            $table->decimal('average_score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Kuesioner Kepuasan Mitra & Evaluasi Mahasiswa (Surveys)
        Schema::create('internship_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->onDelete('cascade');
            $table->foreignId('respondent_id')->constrained('users')->noActionOnDelete();
            $table->string('respondent_type'); // 'industry' or 'student'
            $table->integer('q1_rating')->default(5);
            $table->integer('q2_rating')->default(5);
            $table->integer('q3_rating')->default(5);
            $table->integer('q4_rating')->default(5);
            $table->integer('q5_rating')->default(5);
            $table->decimal('overall_score', 4, 2)->default(5.00);
            $table->text('feedback_text')->nullable();
            $table->boolean('recommendation')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_surveys');
        Schema::dropIfExists('internship_defense_scores');
        Schema::dropIfExists('internship_defenses');
        Schema::dropIfExists('self_proposed_internships');
    }
};
