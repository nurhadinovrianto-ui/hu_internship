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
        // Make internship_id nullable for pre-placement DPL assignments
        try {
            DB::statement("ALTER TABLE dpl_assignments ALTER COLUMN internship_id BIGINT NULL");
        } catch (\Throwable $e) {
            // Ignore if already nullable
        }

        Schema::table('dpl_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('dpl_assignments', 'student_id')) {
                $table->foreignId('student_id')->nullable()->after('internship_id')->constrained('students')->cascadeOnDelete();
            }
            if (!Schema::hasColumn('dpl_assignments', 'academic_period_id')) {
                $table->foreignId('academic_period_id')->nullable()->after('student_id')->constrained('academic_periods')->nullOnDelete();
            }
        });

        // Backfill student_id and academic_period_id from existing internships
        try {
            DB::statement("
                UPDATE d
                SET d.student_id = i.student_id,
                    d.academic_period_id = i.academic_period_id
                FROM dpl_assignments d
                INNER JOIN internships i ON d.internship_id = i.id
                WHERE d.student_id IS NULL
            ");
        } catch (\Throwable $e) {
            // Fallback for non-SQLServer if tested elsewhere
            $assignments = DB::table('dpl_assignments')->whereNotNull('internship_id')->whereNull('student_id')->get();
            foreach ($assignments as $a) {
                $intern = DB::table('internships')->where('id', $a->internship_id)->first();
                if ($intern) {
                    DB::table('dpl_assignments')->where('id', $a->id)->update([
                        'student_id' => $intern->student_id,
                        'academic_period_id' => $intern->academic_period_id,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dpl_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('dpl_assignments', 'academic_period_id')) {
                $table->dropForeign(['academic_period_id']);
                $table->dropColumn('academic_period_id');
            }
            if (Schema::hasColumn('dpl_assignments', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }
};
