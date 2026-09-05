<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Selalu jalankan seeder inti yang esensial
        $this->call([
            RolePermissionSeeder::class,  // 1. Roles & Permissions dulu
            SettingSeeder::class,         // 2. Pengaturan aplikasi default
        ]);

        if (app()->environment('production')) {
            // Jika di production, cukup buat 1 Super Admin.
            // Data Master lainnya (Fakultas, Prodi, Mahasiswa) akan di-import via Excel oleh BAAK.
            $admin = \App\Models\User::firstOrCreate(
                ['email' => 'admin@simang.ac.id'],
                [
                    'name' => 'Super Administrator',
                    'password' => \Illuminate\Support\Facades\Hash::make('password'),
                    'status' => 'active',
                ]
            );
            $admin->assignRole('super-admin');
            
            $this->command->info('');
            $this->command->info('🎓 SIMANG Production Database Seeded Successfully!');
            $this->command->info('Super Admin Email: admin@simang.ac.id | Password: password');
        } else {
            // Jika di local/development, jalankan dummy seeder
            $this->call([
                AcademicSeeder::class,         // Fakultas, Prodi, Periode dummy
                IndustrySeeder::class,         // Data industri mitra dummy
                UserSeeder::class,             // Users dummy untuk semua role
            ]);

            $this->command->info('');
            $this->command->info('🎓 SIMANG Local/Dev Database Seeded Successfully!');
            $this->command->info('Access at: http://simang.test');
        }
    }
}
