<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make industry_supervisor_id nullable
        try {
            DB::statement("ALTER TABLE vacancies ALTER COLUMN industry_supervisor_id BIGINT NULL");
        } catch (\Throwable $e) {
            // Ignore if already nullable
        }

        Schema::table('vacancies', function (Blueprint $table) {
            if (!Schema::hasColumn('vacancies', 'study_program_id')) {
                $table->foreignId('study_program_id')->nullable()->after('academic_period_id')->constrained('study_programs')->nullOnDelete();
            }
            if (!Schema::hasColumn('vacancies', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('study_program_id')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            if (Schema::hasColumn('vacancies', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('vacancies', 'study_program_id')) {
                $table->dropForeign(['study_program_id']);
                $table->dropColumn('study_program_id');
            }
        });
    }
};
