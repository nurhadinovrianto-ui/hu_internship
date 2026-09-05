<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data Perusahaan/Industri Mitra
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('industry_type'); // Bidang industri (IT, Finance, dll)
            $table->string('address');
            $table->string('city');
            $table->string('province')->nullable();
            $table->string('website')->nullable();
            $table->string('email');
            $table->string('phone', 20);
            $table->string('contact_person')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->enum('partnership_status', ['mou', 'moa', 'none'])->default('none');
            $table->date('mou_start_date')->nullable();
            $table->date('mou_end_date')->nullable();
            $table->string('mou_document')->nullable(); // file path
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 11, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Akun Supervisor dari Industri
        Schema::create('industry_supervisors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('industry_id')->constrained();
            $table->string('position')->nullable(); // Jabatan di perusahaan
            $table->string('division')->nullable(); // Divisi
            $table->boolean('is_primary')->default(false); // Supervisor utama
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('industry_supervisors');
        Schema::dropIfExists('industries');
    }
};
