<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('final_reports', function (Blueprint $table) {
            if (!Schema::hasColumn('final_reports', 'report_type')) {
                $table->enum('report_type', ['dpl', 'industry'])->default('dpl')->after('student_id');
            }
        });

        Schema::create('final_report_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_report_id')->constrained('final_reports')->cascadeOnDelete();
            $table->unsignedInteger('version')->default(1);
            $table->string('title');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('status')->default('submitted');
            $table->text('feedback')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_report_revisions');

        Schema::table('final_reports', function (Blueprint $table) {
            if (Schema::hasColumn('final_reports', 'report_type')) {
                $table->dropColumn('report_type');
            }
        });
    }
};
