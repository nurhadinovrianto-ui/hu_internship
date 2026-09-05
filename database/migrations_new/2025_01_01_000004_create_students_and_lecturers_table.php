<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Profil Mahasiswa
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('nim', 30)->unique(); // Nomor Induk Mahasiswa
            $table->string('batch', 10)->nullable(); // Angkatan e.g. "2021"
            $table->integer('current_semester')->default(1);
            $table->integer('total_sks')->default(0); // Total SKS lulus (diisi BAAK)
            $table->decimal('gpa', 4, 2)->default(0.00); // IPK
            $table->string('address')->nullable();
            $table->string('emergency_contact', 20)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });

        // Profil Dosen / DPL
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained()->restrictOnDelete();
            $table->string('nip', 30)->unique()->nullable(); // Nomor Induk Pegawai
            $table->string('nidn', 20)->unique()->nullable();
            $table->string('position')->nullable(); // Jabatan akademik
            $table->string('specialization')->nullable(); // Bidang keahlian
            $table->integer('max_mentee')->default(5); // Maks mahasiswa bimbingan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturers');
        Schema::dropIfExists('students');
    }
};
