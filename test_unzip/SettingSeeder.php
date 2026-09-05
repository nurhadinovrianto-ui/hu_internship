<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // GENERAL
            [
                'key' => 'app_name',
                'value' => 'Internship Management System',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama lengkap aplikasi/sistem yang digunakan di seluruh header dan judul.',
                'group' => 'general'
            ],
            [
                'key' => 'app_short_name',
                'value' => 'SIMANG',
                'label' => 'Singkatan Aplikasi',
                'description' => 'Singkatan nama aplikasi untuk logo dan ruang terbatas.',
                'group' => 'general'
            ],
            [
                'key' => 'system_version',
                'value' => 'V3.0',
                'label' => 'Versi Sistem',
                'description' => 'Versi rilis sistem saat ini.',
                'group' => 'general'
            ],
            [
                'key' => 'jitsi_domain',
                'value' => 'meet.jit.si',
                'label' => 'Domain Server Jitsi Meet',
                'description' => 'Domain server Jitsi Meet yang digunakan untuk Online Meeting (default: meet.jit.si).',
                'group' => 'general'
            ],
            [
                'key' => 'app_logo',
                'value' => '',
                'label' => 'Logo Aplikasi (Header & Navbar)',
                'description' => 'Unggah gambar logo utama aplikasi (format PNG/JPG).',
                'group' => 'general'
            ],
            [
                'key' => 'app_icon',
                'value' => '',
                'label' => 'Ikon Aplikasi (Favicon)',
                'description' => 'Unggah ikon kecil aplikasi untuk tab browser / PWA.',
                'group' => 'general'
            ],
            [
                'key' => 'app_letterhead',
                'value' => '',
                'label' => 'Kop Surat Resmi Kampus',
                'description' => 'Unggah gambar kop surat resmi untuk cetak surat pengantar & sertifikat.',
                'group' => 'general'
            ],

            // ACADEMIC & INTERNSHIP RULES
            [
                'key' => 'min_sks',
                'value' => '110',
                'label' => 'Batas Minimum SKS',
                'description' => 'Batas minimum SKS lulus bagi mahasiswa untuk mendaftar magang.',
                'group' => 'academic'
            ],
            [
                'key' => 'min_gpa',
                'value' => '3.00',
                'label' => 'Batas Minimum IPK (GPA)',
                'description' => 'Batas minimum IPK/GPA mahasiswa untuk mendaftar magang.',
                'group' => 'academic'
            ],
            [
                'key' => 'min_days_vacancy_deadline',
                'value' => '7',
                'label' => 'Minimal Waktu Batas Lamaran (Hari)',
                'description' => 'Jumlah hari minimal dari hari ini untuk batas akhir melamar lowongan.',
                'group' => 'academic'
            ],
            [
                'key' => 'max_active_applications',
                'value' => '3',
                'label' => 'Maksimal Lamaran Aktif Mahasiswa',
                'description' => 'Jumlah maksimal lamaran aktif yang boleh dilamar mahasiswa secara bersamaan.',
                'group' => 'academic'
            ],
            [
                'key' => 'grade_weight_industry',
                'value' => '60',
                'label' => 'Bobot Nilai Industri (%)',
                'description' => 'Persentase bobot penilaian dari Pembimbing/Supervisor Industri (total dengan DPL harus 100%).',
                'group' => 'academic'
            ],
            [
                'key' => 'grade_weight_dpl',
                'value' => '40',
                'label' => 'Bobot Nilai DPL (%)',
                'description' => 'Persentase bobot penilaian dari Dosen Pembimbing Lapangan (total dengan Industri harus 100%).',
                'group' => 'academic'
            ],
            [
                'key' => 'max_cv_size_kb',
                'value' => '5120',
                'label' => 'Maksimal Ukuran Upload CV / Transkrip (KB)',
                'description' => 'Batas ukuran file maksimal untuk upload dokumen lamaran (dalam KB).',
                'group' => 'academic'
            ],
            [
                'key' => 'max_logbook_size_kb',
                'value' => '5120',
                'label' => 'Maksimal Ukuran Lampiran Logbook (KB)',
                'description' => 'Batas ukuran file maksimal untuk lampiran foto/dokumen logbook (dalam KB).',
                'group' => 'academic'
            ],
            [
                'key' => 'max_report_size_kb',
                'value' => '10240',
                'label' => 'Maksimal Ukuran Laporan Akhir (KB)',
                'description' => 'Batas ukuran file maksimal untuk laporan akhir magang (dalam KB).',
                'group' => 'academic'
            ],
            [
                'key' => 'use_campus_geofencing',
                'value' => '0',
                'label' => 'Aktifkan Pembatasan Geofencing Kampus (0=Tidak / Dimatikan, 1=Ya)',
                'description' => 'Apakah absensi ke kampus dibatasi oleh radius koordinat tertentu.',
                'group' => 'academic'
            ],
            [
                'key' => 'use_industry_geofencing',
                'value' => '0',
                'label' => 'Aktifkan Pembatasan Geofencing Industri (0=Tidak / Dimatikan, 1=Ya)',
                'description' => 'Apakah absensi ke lokasi industri dibatasi oleh radius koordinat kantor.',
                'group' => 'academic'
            ],
            [
                'key' => 'geofence_radius_meters',
                'value' => '500',
                'label' => 'Radius Geofencing (Meter)',
                'description' => 'Jarak toleransi maksimal absensi dari koordinat lokasi magang/kampus.',
                'group' => 'academic'
            ],
            [
                'key' => 'campus_latitude',
                'value' => '-6.2088',
                'label' => 'Latitude Pusat Geofencing',
                'description' => 'Koordinat garis lintang (latitude) default kampus/pusat.',
                'group' => 'academic'
            ],
            [
                'key' => 'campus_longitude',
                'value' => '106.8456',
                'label' => 'Longitude Pusat Geofencing',
                'description' => 'Koordinat garis bujur (longitude) default kampus/pusat.',
                'group' => 'academic'
            ],

            // CONTACT
            [
                'key' => 'contact_email',
                'value' => 'support@horizon.ac.id',
                'label' => 'Email Kontak Hubungan',
                'description' => 'Alamat email resmi untuk bantuan/kontak sistem.',
                'group' => 'contact'
            ],
            [
                'key' => 'contact_phone',
                'value' => '021-1234567',
                'label' => 'Telepon Kontak Hubungan',
                'description' => 'Nomor telepon resmi untuk bantuan/kontak sistem.',
                'group' => 'contact'
            ],

            // GOOGLE SSO
            [
                'key' => 'google_client_id',
                'value' => env('GOOGLE_CLIENT_ID', ''),
                'label' => 'Google Client ID',
                'description' => 'Client ID dari konsol Google Cloud API Credentials.',
                'group' => 'google'
            ],
            [
                'key' => 'google_client_secret',
                'value' => env('GOOGLE_CLIENT_SECRET', ''),
                'label' => 'Google Client Secret',
                'description' => 'Client Secret dari konsol Google Cloud API Credentials.',
                'group' => 'google'
            ],
            [
                'key' => 'google_redirect_uri',
                'value' => env('GOOGLE_REDIRECT_URI', 'https://kms-fict.horizon.ac.id/internship/auth/google/callback'),
                'label' => 'Google Redirect URI',
                'description' => 'Redirect URI callback yang didaftarkan di konsol Google Cloud API.',
                'group' => 'google'
            ]
        ];

        foreach ($settings as $s) {
            Setting::firstOrCreate(['key' => $s['key']], $s);
        }
    }
}
