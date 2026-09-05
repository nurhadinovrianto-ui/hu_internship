<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\StudyProgram;
use App\Models\IndustrySupervisor;
use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $tiProdi = StudyProgram::where('code', 'TI')->first();

        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@simang.ac.id'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $admin->assignRole('super-admin');

        // Finance
        $finance = User::firstOrCreate(
            ['email' => 'finance@simang.ac.id'],
            [
                'name' => 'Bagian Keuangan',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $finance->assignRole('finance');

        // BAAK
        $baak = User::firstOrCreate(
            ['email' => 'baak@simang.ac.id'],
            [
                'name' => 'Bagian BAAK',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $baak->assignRole('baak');

        // Kaprodi
        $kaprodi = User::firstOrCreate(
            ['email' => 'kaprodi@simang.ac.id'],
            [
                'name' => 'Dr. Ahmad Fauzi, M.Kom',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $kaprodi->assignRole('kaprodi');
        if ($tiProdi) {
            $tiProdi->update(['head_user_id' => $kaprodi->id, 'head_name' => $kaprodi->name]);
        }

        // Dekan
        $dekan = User::firstOrCreate(
            ['email' => 'dekan@simang.ac.id'],
            [
                'name' => 'Prof. Dr. Budi Santoso, M.T',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $dekan->assignRole('dekan');
        $fictFaculty = \App\Models\Faculty::where('code', 'FICT')->first();
        if ($fictFaculty) {
            $fictFaculty->update(['dean_user_id' => $dekan->id, 'dean_name' => $dekan->name]);
        }

        // DPL
        $dpl = User::firstOrCreate(
            ['email' => 'dpl@simang.ac.id'],
            [
                'name' => 'Dr. Sari Indrawati, M.Sc',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $dpl->assignRole('dpl');
        if ($tiProdi) {
            Lecturer::firstOrCreate(
                ['user_id' => $dpl->id],
                [
                    'study_program_id' => $tiProdi->id,
                    'nidn' => '0123456789',
                    'position' => 'Lektor',
                    'specialization' => 'Software Engineering',
                    'max_mentee' => 10,
                ]
            );
        }

        // Supervisor Industri
        $industry = Industry::first();
        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@industri.com'],
            [
                'name' => 'Dian Pramono',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $supervisor->assignRole('supervisor-industri');
        if ($industry) {
            IndustrySupervisor::firstOrCreate(
                ['user_id' => $supervisor->id],
                [
                    'industry_id' => $industry->id,
                    'position' => 'HR Manager',
                    'division' => 'Human Resources',
                    'is_primary' => true,
                ]
            );
        }

        // Mahasiswa Demo
        $mahasiswa = User::firstOrCreate(
            ['email' => 'mahasiswa@simang.ac.id'],
            [
                'name' => 'Andi Pratama',
                'password' => Hash::make('password'),
                'status' => 'active',
            ]
        );
        $mahasiswa->assignRole('mahasiswa');
        if ($tiProdi) {
            Student::firstOrCreate(
                ['user_id' => $mahasiswa->id],
                [
                    'study_program_id' => $tiProdi->id,
                    'nim' => '2021100001',
                    'batch' => '2021',
                    'current_semester' => 7,
                    'total_sks' => 110,
                    'gpa' => 3.45,
                ]
            );
        }

        $this->command->info('✅ Users seeded! Login credentials:');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Super Admin', 'admin@simang.ac.id', 'password'],
                ['Finance', 'finance@simang.ac.id', 'password'],
                ['BAAK', 'baak@simang.ac.id', 'password'],
                ['Kaprodi', 'kaprodi@simang.ac.id', 'password'],
                ['Dekan', 'dekan@simang.ac.id', 'password'],
                ['DPL', 'dpl@simang.ac.id', 'password'],
                ['Supervisor Industri', 'supervisor@industri.com', 'password'],
                ['Mahasiswa', 'mahasiswa@simang.ac.id', 'password'],
            ]
        );
    }
}
