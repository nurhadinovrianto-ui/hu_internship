<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained()->cascadeOnDelete();
            $table->string('agreement_number')->nullable();
            $table->string('title')->default('Perjanjian Kerja Sama Magang');
            $table->string('document_file')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('allowance')->nullable();
            $table->enum('status', ['draft', 'active', 'completed', 'terminated'])->default('active');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_agreements');
    }
};
