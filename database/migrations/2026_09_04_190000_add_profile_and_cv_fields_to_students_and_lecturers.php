<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('cv_file')->nullable()->after('photo');
            $table->string('transcript_file')->nullable()->after('cv_file');
            $table->string('portfolio_url')->nullable()->after('transcript_file');
            $table->string('linkedin_url')->nullable()->after('portfolio_url');
            $table->string('github_url')->nullable()->after('linkedin_url');
            $table->text('skills')->nullable()->after('github_url');
            $table->text('bio')->nullable()->after('skills');
        });

        \Illuminate\Support\Facades\DB::statement("ALTER TABLE students ALTER COLUMN emergency_contact VARCHAR(100) NULL");

        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('cv_file')->nullable()->after('max_mentee');
            $table->string('office_room')->nullable()->after('cv_file');
            $table->string('scholar_url')->nullable()->after('office_room');
            $table->string('sinta_url')->nullable()->after('scholar_url');
            $table->string('linkedin_url')->nullable()->after('sinta_url');
            $table->text('bio')->nullable()->after('linkedin_url');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'cv_file',
                'transcript_file',
                'portfolio_url',
                'linkedin_url',
                'github_url',
                'skills',
                'bio',
            ]);
        });

        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn([
                'cv_file',
                'office_room',
                'scholar_url',
                'sinta_url',
                'linkedin_url',
                'bio',
            ]);
        });
    }
};
