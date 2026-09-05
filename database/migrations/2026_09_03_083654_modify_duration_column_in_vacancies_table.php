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
        Schema::table('vacancies', function (Blueprint $table) {
            // Tambah kolom baru
            $table->string('duration')->nullable()->after('quota');
        });

        // Pindahkan data lama dan tambahkan " Bulan" (untuk SQL Server menggunakan concat +)
        \Illuminate\Support\Facades\DB::statement("UPDATE vacancies SET duration = CAST(duration_months AS VARCHAR(10)) + ' Bulan'");

        Schema::table('vacancies', function (Blueprint $table) {
            // Buat tidak nullable setelah diisi
            $table->string('duration')->nullable(false)->change();
            // Hapus kolom lama
            $table->dropColumn('duration_months');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->integer('duration_months')->nullable()->after('quota');
        });

        // Asumsi data lama mungkin berformat "3 Bulan", kita coba ekstrak angka pertamanya atau reset saja
        // Kita tidak bisa 100% reverse tanpa fungsi string kompleks, jadi untuk down() disederhanakan:
        \Illuminate\Support\Facades\DB::statement("UPDATE vacancies SET duration_months = TRY_CAST(SUBSTRING(duration, 1, CHARINDEX(' ', duration + ' ') - 1) AS INT)");

        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn('duration');
        });
    }
};
