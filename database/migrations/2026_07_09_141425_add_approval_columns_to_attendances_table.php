<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('location_type')->default('industry')->after('work_duration_minutes');
            $table->string('approval_status')->default('approved')->after('location_type');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['location_type', 'approval_status', 'approved_by']);
        });
    }
};
