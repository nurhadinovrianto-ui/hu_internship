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
        // 1. Tabel Lokasi Terkini Mahasiswa (Live / Latest Location)
        Schema::create('student_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->foreignId('internship_id')->nullable()->constrained('internships')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->float('accuracy')->nullable();       // radius akurasi dalam meter
            $table->float('speed')->nullable();          // kecepatan pergerakan (m/s atau km/h)
            $table->float('heading')->nullable();        // derajat arah kompas (0-360)
            $table->integer('battery_level')->nullable();// persentase baterai HP (0-100)
            $table->string('status', 20)->default('online'); // online, idle, offline
            $table->timestamp('last_ping_at')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Rekam Jejak Koordinat (Historical Breadcrumbs Route)
        Schema::create('student_location_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('internship_id')->nullable()->constrained('internships')->nullOnDelete();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 11, 7);
            $table->float('accuracy')->nullable();
            $table->float('speed')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['student_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_location_logs');
        Schema::dropIfExists('student_locations');
    }
};
