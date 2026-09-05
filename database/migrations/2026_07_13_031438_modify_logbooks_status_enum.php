<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify logbooks status enum to include revision_required
        // DB::statement("ALTER TABLE logbooks MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed_dpl', 'reviewed_industry', 'revision_required') DEFAULT 'submitted'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE logbooks MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed_dpl', 'reviewed_industry', 'revision_required') DEFAULT 'submitted'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change back to original enum
        // DB::statement("ALTER TABLE logbooks MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed_dpl', 'reviewed_industry') DEFAULT 'submitted'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE logbooks MODIFY COLUMN status ENUM('draft', 'submitted', 'reviewed_dpl', 'reviewed_industry') DEFAULT 'submitted'");
        }
    }
};
