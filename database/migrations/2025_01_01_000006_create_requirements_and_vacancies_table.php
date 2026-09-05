<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Status Syarat Gatekeeper per Mahasiswa per Periode
        Schema::create('student_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained()->cascadeOnDelete();

            // Finance Gatekeeper
            $table->boolean('payment_cleared')->default(false); // Lunas SPP
            $table->date('payment_verified_at')->nullable();
            $table->foreignId('payment_verified_by')->nullable()->constrained('users'); // Finance officer

            // BAAK Gatekeeper
            $table->integer('sks_completed')->default(0); // SKS yang sudah lulus
            $table->integer('sks_minimum')->default(100); // Minimum SKS untuk magang
            $table->boolean('sks_eligible')->default(false); // Otomatis berdasar sks_completed >= sks_minimum
            $table->date('sks_verified_at')->nullable();
            $table->foreignId('sks_verified_by')->nullable()->constrained('users'); // BAAK officer

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_period_id']); // Satu record per mahasiswa per periode
        });

        // Lowongan Magang dari Industri
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained()->cascadeOnDelete();
            $table->foreignId('industry_supervisor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained();
            $table->string('title'); // Judul lowongan
            $table->string('position'); // Posisi yang dilamar
            $table->string('division')->nullable(); // Divisi
            $table->text('description'); // Deskripsi pekerjaan
            $table->text('requirements'); // Persyaratan
            $table->integer('quota'); // Kuota
            $table->integer('duration_months'); // Durasi magang (bulan)
            $table->date('start_date')->nullable(); // Estimasi mulai
            $table->date('apply_deadline'); // Batas melamar
            $table->enum('work_type', ['onsite', 'remote', 'hybrid'])->default('onsite');
            $table->string('location')->nullable(); // Lokasi jika onsite
            $table->boolean('is_published')->default(true);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('student_requirements');
    }
};
