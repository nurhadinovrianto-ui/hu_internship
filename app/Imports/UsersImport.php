<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\IndustrySupervisor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection, WithHeadingRow
{
    protected $role;

    public function __construct(string $role)
    {
        $this->role = $role;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Skip empty rows
                if (!isset($row['email']) || empty($row['email'])) {
                    continue;
                }

                // If Role is Staff (super-admin, finance, baak), role comes from row if provided, else from selected
                $roleToAssign = $this->role;
                if ($this->role === 'staff' && isset($row['role']) && !empty($row['role'])) {
                    $roleToAssign = strtolower($row['role']);
                }
                
                // Password default to nim/nip if available, or password123
                $password = 'password123';
                if ($this->role === 'mahasiswa' && isset($row['nim'])) {
                    $password = $row['nim'];
                } elseif (in_array($this->role, ['dpl', 'kaprodi', 'dekan']) && isset($row['nip'])) {
                    $password = $row['nip'];
                }

                // Create or Update User
                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['name'] ?? 'Unknown',
                        'phone' => $row['phone'] ?? null,
                        'password' => Hash::make($password),
                        'status' => 'active',
                    ]
                );

                // Assign Role
                if ($roleToAssign !== 'staff') {
                    $user->syncRoles([$roleToAssign]);
                }

                // Create Profile based on Role
                if ($this->role === 'mahasiswa') {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nim' => $row['nim'] ?? null,
                            'batch' => $row['batch'] ?? null,
                            'current_semester' => $row['current_semester'] ?? null,
                            'total_sks' => $row['total_sks'] ?? 0,
                            'gpa' => $row['gpa'] ?? 0,
                            'study_program_id' => $row['study_program_id'] ?? null,
                        ]
                    );
                } elseif (in_array($this->role, ['dpl', 'kaprodi', 'dekan'])) {
                    Lecturer::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nip' => $row['nip'] ?? null,
                            'nidn' => $row['nidn'] ?? null,
                            'position' => $row['position'] ?? null,
                            'specialization' => $row['specialization'] ?? null,
                            'max_mentee' => $row['max_mentee'] ?? 10,
                            'study_program_id' => $row['study_program_id'] ?? null,
                        ]
                    );
                } elseif ($this->role === 'supervisor-industri') {
                    IndustrySupervisor::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'industry_id' => $row['industry_id'] ?? null,
                            'position' => $row['position'] ?? null,
                            'division' => $row['division'] ?? null,
                        ]
                    );
                }
            }
        });
    }
}
