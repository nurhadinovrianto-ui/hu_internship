<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('industry_certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->unique()->constrained('industries')->cascadeOnDelete();
            $table->string('background_image')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_position')->nullable();
            $table->string('seal_image')->nullable();
            $table->timestamps();
        });

        Schema::table('certificates', function (Blueprint $table) {
            if (!Schema::hasColumn('certificates', 'issuance_type')) {
                $table->string('issuance_type')->default('auto_generated')->after('file_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'issuance_type')) {
                $table->dropColumn('issuance_type');
            }
        });

        Schema::dropIfExists('industry_certificate_templates');
    }
};
