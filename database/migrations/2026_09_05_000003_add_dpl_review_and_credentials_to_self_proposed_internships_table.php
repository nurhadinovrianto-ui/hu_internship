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
        Schema::table('self_proposed_internships', function (Blueprint $table) {
            if (!Schema::hasColumn('self_proposed_internships', 'dpl_id')) {
                $table->foreignId('dpl_id')->nullable()->after('academic_period_id')->constrained('lecturers')->noActionOnDelete();
            }
            if (!Schema::hasColumn('self_proposed_internships', 'dpl_status')) {
                $table->string('dpl_status')->default('pending')->after('status'); // pending, approved, revision, rejected
            }
            if (!Schema::hasColumn('self_proposed_internships', 'dpl_notes')) {
                $table->text('dpl_notes')->nullable()->after('dpl_status');
            }
            if (!Schema::hasColumn('self_proposed_internships', 'dpl_reviewed_at')) {
                $table->dateTime('dpl_reviewed_at')->nullable()->after('dpl_notes');
            }
            if (!Schema::hasColumn('self_proposed_internships', 'partner_user_id')) {
                $table->foreignId('partner_user_id')->nullable()->after('internship_id')->constrained('users')->noActionOnDelete();
            }
            if (!Schema::hasColumn('self_proposed_internships', 'partner_temp_password')) {
                $table->string('partner_temp_password')->nullable()->after('partner_user_id');
            }
            if (!Schema::hasColumn('self_proposed_internships', 'partner_account_created')) {
                $table->boolean('partner_account_created')->default(false)->after('partner_temp_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('self_proposed_internships', function (Blueprint $table) {
            if (Schema::hasColumn('self_proposed_internships', 'partner_user_id')) {
                $table->dropForeign(['partner_user_id']);
                $table->dropColumn('partner_user_id');
            }
            if (Schema::hasColumn('self_proposed_internships', 'dpl_id')) {
                $table->dropForeign(['dpl_id']);
                $table->dropColumn('dpl_id');
            }
            $table->dropColumn([
                'dpl_status',
                'dpl_notes',
                'dpl_reviewed_at',
                'partner_temp_password',
                'partner_account_created',
            ]);
        });
    }
};
