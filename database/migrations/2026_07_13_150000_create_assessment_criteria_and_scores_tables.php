<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('weight', 5, 2)->default(20.00); // Bobot dalam persentase (0 - 100)
            $table->enum('assessor_type', ['dpl', 'industry']);
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->foreignId('academic_period_id')->nullable()->constrained('academic_periods')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnDelete();
            $table->foreignId('assessment_criterion_id')->constrained('assessment_criteria')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['assessment_id', 'assessment_criterion_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_scores');
        Schema::dropIfExists('assessment_criteria');
    }
};
