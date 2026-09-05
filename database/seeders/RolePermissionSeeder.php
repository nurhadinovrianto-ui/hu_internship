<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definisi Permissions
        $permissions = [
            // User management
            'manage-users', 'view-users',
            // Academic
            'manage-faculties', 'manage-study-programs', 'manage-periods',
            // Industry
            'manage-industries', 'view-industries',
            // Finance
            'validate-payments',
            // BAAK
            'input-sks', 'process-grade-conversion',
            // Kaprodi
            'validate-applications', 'assign-dpl', 'view-prodi-statistics',
            // Dekan
            'view-faculty-statistics',
            // DPL
            'review-logbook-dpl', 'assess-student-dpl',
            // Industri
            'manage-vacancies', 'select-applicants', 'review-logbook-industry', 'assess-student-industry',
            // Mahasiswa
            'apply-internship', 'submit-attendance', 'submit-logbook', 'upload-report', 'download-certificate',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Definisi Roles & assign permissions
        $roles = [
            'super-admin' => $permissions, // All permissions
            'finance' => ['validate-payments', 'view-users'],
            'baak' => ['input-sks', 'process-grade-conversion', 'view-users'],
            'kaprodi' => ['validate-applications', 'assign-dpl', 'view-prodi-statistics', 'view-users'],
            'dekan' => ['view-faculty-statistics', 'view-industries', 'view-users'],
            'dpl' => ['review-logbook-dpl', 'assess-student-dpl', 'view-users'],
            'supervisor-industri' => ['manage-vacancies', 'select-applicants', 'review-logbook-industry', 'assess-student-industry'],
            'mahasiswa' => ['apply-internship', 'submit-attendance', 'submit-logbook', 'upload-report', 'download-certificate'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }

        $this->command->info('✅ Roles & Permissions seeded successfully!');
    }
}
