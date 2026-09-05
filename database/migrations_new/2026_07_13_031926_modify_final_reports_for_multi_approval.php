<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modify status enum
        DB::statement("ALTER TABLE final_reports MODIFY COLUMN status ENUM('submitted', 'reviewed', 'approved', 'revision', 'industry_approved', 'dpl_approved', 'kaprodi_received') DEFAULT 'submitted'");

        Schema::table('final_reports', function (Blueprint $table) {
            $table->timestamp('industry_approved_at')->nullable();
            $table->timestamp('dpl_approved_at')->nullable();
            $table->timestamp('kaprodi_submitted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('final_reports', function (Blueprint $table) {
            $table->dropColumn(['industry_approved_at', 'dpl_approved_at', 'kaprodi_submitted_at']);
        });

        // Revert status enum
        DB::statement("ALTER TABLE final_reports MODIFY COLUMN status ENUM('submitted', 'reviewed', 'approved', 'revision') DEFAULT 'submitted'");
    }
};
