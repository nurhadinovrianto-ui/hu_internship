<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssessmentCriterion;

class AssessmentCriterionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Kriteria Default Industri
        $industryDefaults = [
            ['name' => 'Kedisiplinan & Kehadiran', 'weight' => 20.00, 'sort_order' => 1],
            ['name' => 'Keterampilan / Skill Teknis', 'weight' => 30.00, 'sort_order' => 2],
            ['name' => 'Sikap & Etika Kerja (Attitude)', 'weight' => 20.00, 'sort_order' => 3],
            ['name' => 'Kerjasama Tim (Teamwork)', 'weight' => 15.00, 'sort_order' => 4],
            ['name' => 'Inisiatif & Kemandirian', 'weight' => 15.00, 'sort_order' => 5],
        ];

        foreach ($industryDefaults as $item) {
            AssessmentCriterion::firstOrCreate(
                [
                    'name' => $item['name'],
                    'assessor_type' => 'industry',
                    'industry_id' => null,
                ],
                [
                    'weight' => $item['weight'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }

        // 2. Kriteria Default DPL
        $dplDefaults = [
            ['name' => 'Laporan Akhir Magang', 'weight' => 40.00, 'sort_order' => 1],
            ['name' => 'Presentasi Akhir & Tanya Jawab', 'weight' => 30.00, 'sort_order' => 2],
            ['name' => 'Kelengkapan & Rutinitas Logbook', 'weight' => 30.00, 'sort_order' => 3],
        ];

        foreach ($dplDefaults as $item) {
            AssessmentCriterion::firstOrCreate(
                [
                    'name' => $item['name'],
                    'assessor_type' => 'dpl',
                    'academic_period_id' => null,
                ],
                [
                    'weight' => $item['weight'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ]
            );
        }
    }
}
