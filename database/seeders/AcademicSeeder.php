<?php

namespace Database\Seeders;

use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // Fakultas
        $faculties = [
            ['name' => 'Fakultas Ilmu Komputer dan Teknologi Informasi', 'code' => 'FICT'],
            ['name' => 'Fakultas Ekonomi dan Bisnis', 'code' => 'FEB'],
            ['name' => 'Fakultas Teknik', 'code' => 'FT'],
        ];

        foreach ($faculties as $f) {
            $faculty = Faculty::firstOrCreate(['code' => $f['code']], $f + ['status' => 'active']);

            // Program Studi per Fakultas
            $programs = match($f['code']) {
                'FICT' => [
                    ['name' => 'Teknik Informatika', 'code' => 'IF', 'degree' => 'S1'],
                    ['name' => 'Sistem Informasi', 'code' => 'SI', 'degree' => 'S1'],
                    ['name' => 'Teknologi Informasi', 'code' => 'TI', 'degree' => 'S1'],
                ],
                'FMB' => [
                    ['name' => 'Manajemen', 'code' => 'MNJ', 'degree' => 'S1'],
                    ['name' => 'Akuntansi', 'code' => 'AKT', 'degree' => 'S1'],
                ],
                'FHS' => [
                    ['name' => 'Keperawatan', 'code' => 'KEP', 'degree' => 'S1'],
                    ['name' => 'Sarjana Terapan Kebidanan', 'code' => 'BIB', 'degree' => 'D4'],
                ],
                default => [],
            };

            foreach ($programs as $p) {
                StudyProgram::firstOrCreate(
                    ['code' => $p['code']],
                    array_merge($p, ['faculty_id' => $faculty->id, 'status' => 'active'])
                );
            }
        }

        // Periode Akademik Aktif
        AcademicPeriod::firstOrCreate(
            ['year' => '2024/2025', 'semester' => 'genap'],
            [
                'name' => 'Semester Genap 2024/2025',
                'start_date' => '2025-02-01',
                'end_date' => '2025-07-31',
                'apply_start' => '2025-01-15',
                'apply_end' => '2025-03-31',
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Academic data seeded!');
    }
}
