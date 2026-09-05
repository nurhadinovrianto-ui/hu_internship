<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Semester Ganjil 2024/2025"
            $table->string('year', 10); // e.g. "2024/2025"
            $table->enum('semester', ['ganjil', 'genap']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('apply_start')->nullable(); // Batas mulai apply
            $table->date('apply_end')->nullable();   // Batas akhir apply
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_periods');
    }
};
