<?php

namespace Database\Seeders;

use App\Models\Industry;
use App\Models\IndustrySupervisor;
use App\Models\Vacancy;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class IndustrySeeder extends Seeder
{
    public function run(): void
    {
        $industries = [
            [
                'name' => 'PT Telkom Indonesia',
                'slug' => 'pt-telkom-indonesia',
                'industry_type' => 'Teknologi & Telekomunikasi',
                'address' => 'Jl. Japati No.1',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'website' => 'https://www.telkom.co.id',
                'email' => 'recruitment@telkom.co.id',
                'phone' => '022-4521500',
                'contact_person' => 'Bagian Rekrutmen',
                'description' => 'BUMN terkemuka di bidang telekomunikasi Indonesia.',
                'partnership_status' => 'mou',
                'mou_start_date' => '2024-01-01',
                'mou_end_date' => '2026-12-31',
                'latitude' => -6.8993867,
                'longitude' => 107.6190538,
                'is_active' => true,
            ],
            [
                'name' => 'PT Gojek Indonesia',
                'slug' => 'pt-gojek-indonesia',
                'industry_type' => 'Teknologi & Startup',
                'address' => 'Pasaraya Blok M, Gedung B Lt. 6',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'website' => 'https://www.gojek.com',
                'email' => 'internship@gojek.com',
                'phone' => '021-12345678',
                'contact_person' => 'HR Gojek',
                'description' => 'Perusahaan teknologi multi-layanan terbesar di Asia Tenggara.',
                'partnership_status' => 'mou',
                'mou_start_date' => '2024-06-01',
                'mou_end_date' => '2026-05-31',
                'latitude' => -6.2443419,
                'longitude' => 106.8021175,
                'is_active' => true,
            ],
            [
                'name' => 'Bank Central Asia (BCA)',
                'slug' => 'bank-bca',
                'industry_type' => 'Perbankan & Keuangan',
                'address' => 'Jl. MH Thamrin No.1',
                'city' => 'Jakarta Pusat',
                'province' => 'DKI Jakarta',
                'website' => 'https://www.bca.co.id',
                'email' => 'internship@bca.co.id',
                'phone' => '021-2358000',
                'contact_person' => 'Divisi HR BCA',
                'description' => 'Bank swasta terbesar di Indonesia.',
                'partnership_status' => 'moa',
                'mou_start_date' => '2023-09-01',
                'mou_end_date' => '2025-08-31',
                'latitude' => -6.1950269,
                'longitude' => 106.8229716,
                'is_active' => true,
            ],
        ];

        foreach ($industries as $data) {
            Industry::firstOrCreate(['slug' => $data['slug']], $data);
        }

        // Buat beberapa lowongan demo
        $period = AcademicPeriod::where('is_active', true)->first();
        $telkom = Industry::where('slug', 'pt-telkom-indonesia')->first();
        $gojek = Industry::where('slug', 'pt-gojek-indonesia')->first();

        if ($period && $telkom) {
            $supervisor = $telkom->supervisors()->first();
            if ($supervisor) {
                Vacancy::firstOrCreate(
                    ['title' => 'Software Developer Intern', 'industry_id' => $telkom->id],
                    [
                        'industry_supervisor_id' => $supervisor->id,
                        'academic_period_id' => $period->id,
                        'position' => 'Software Developer',
                        'division' => 'Digital Products',
                        'description' => 'Intern sebagai Software Developer di divisi Digital Products. Akan terlibat dalam pengembangan aplikasi web dan mobile.',
                        'requirements' => "- Mahasiswa aktif semester 5 ke atas\n- Menguasai minimal satu bahasa pemrograman\n- Memiliki pengetahuan tentang REST API\n- Mampu bekerja dalam tim",
                        'quota' => 5,
                        'duration' => '3 Bulan',
                        'start_date' => '2025-02-01',
                        'apply_deadline' => '2025-01-25',
                        'work_type' => 'hybrid',
                        'location' => 'Bandung',
                        'is_published' => true,
                        'is_closed' => false,
                    ]
                );
            }
        }

        if ($period && $gojek) {
            $supervisor = $gojek->supervisors()->first();
            if ($supervisor) {
                Vacancy::firstOrCreate(
                    ['title' => 'Data Analyst Intern', 'industry_id' => $gojek->id],
                    [
                        'industry_supervisor_id' => $supervisor->id,
                        'academic_period_id' => $period->id,
                        'position' => 'Data Analyst',
                        'division' => 'Data & Analytics',
                        'description' => 'Intern sebagai Data Analyst, membantu tim dalam menganalisis data operasional dan membuat dashboard insights.',
                        'requirements' => "- Mahasiswa aktif semester 5 ke atas\n- Menguasai SQL dan Python/R\n- Familiar dengan tools visualisasi data (Tableau/Power BI)\n- Kemampuan analisis yang kuat",
                        'quota' => 2,
                        'duration' => '3 Bulan',
                        'start_date' => '2025-02-15',
                        'apply_deadline' => '2025-02-01',
                        'work_type' => 'remote',
                        'location' => 'Remote',
                        'is_published' => true,
                        'is_closed' => false,
                    ]
                );
            }
        }

        $this->command->info('✅ Industries & Vacancies seeded!');
    }
}
