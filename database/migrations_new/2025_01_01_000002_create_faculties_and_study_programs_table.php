<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('dean_name')->nullable(); // Nama Dekan
            $table->foreignId('dean_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('study_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('head_name')->nullable(); // Nama Kaprodi
            $table->foreignId('head_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('degree', ['D3', 'D4', 'S1', 'S2', 'S3'])->default('S1');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_programs');
        Schema::dropIfExists('faculties');
    }
};
