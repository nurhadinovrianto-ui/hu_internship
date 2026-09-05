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
                if ($this->role === 'mahasiswa' && !empty($row['nim'])) {
                    $password = (string) $row['nim'];
                } elseif (in_array($this->role, ['dpl', 'kaprodi', 'dekan']) && !empty($row['nip'])) {
                    $password = (string) $row['nip'];
                }

                // Create or Update User without resetting existing user's password
                $user = User::firstOrNew(['email' => $row['email']]);
                $user->name = !empty($row['name']) ? $row['name'] : ($user->name ?? 'Unknown');
                if (isset($row['phone']) && $row['phone'] !== '') {
                    $user->phone = $row['phone'];
                }
                if (!$user->exists) {
                    $user->password = Hash::make($password);
                    $user->status = 'active';
                }
                $user->save();

                // Assign Role
                if ($roleToAssign !== 'staff') {
                    $user->syncRoles([$roleToAssign]);
                }

                // Create Profile based on Role
                if ($this->role === 'mahasiswa') {
                    Student::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nim' => !empty($row['nim']) ? $row['nim'] : null,
                            'batch' => !empty($row['batch']) ? $row['batch'] : now()->year,
                            'current_semester' => !empty($row['current_semester']) ? (int) $row['current_semester'] : 1,
                            'total_sks' => !empty($row['total_sks']) ? (int) $row['total_sks'] : 0,
                            'gpa' => !empty($row['gpa']) ? (float) $row['gpa'] : 0,
                            'study_program_id' => !empty($row['study_program_id']) ? (int) $row['study_program_id'] : null,
                        ]
                    );
                } elseif (in_array($this->role, ['dpl', 'kaprodi', 'dekan'])) {
                    Lecturer::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'nip' => !empty($row['nip']) ? $row['nip'] : null,
                            'nidn' => !empty($row['nidn']) ? $row['nidn'] : null,
                            'position' => !empty($row['position']) ? $row['position'] : null,
                            'specialization' => !empty($row['specialization']) ? $row['specialization'] : null,
                            'max_mentee' => !empty($row['max_mentee']) ? (int) $row['max_mentee'] : 10,
                            'study_program_id' => !empty($row['study_program_id']) ? (int) $row['study_program_id'] : null,
                        ]
                    );
                } elseif ($this->role === 'supervisor-industri') {
                    IndustrySupervisor::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'industry_id' => !empty($row['industry_id']) ? (int) $row['industry_id'] : null,
                            'position' => !empty($row['position']) ? $row['position'] : null,
                            'division' => !empty($row['division']) ? $row['division'] : null,
                        ]
                    );
                }
            }
        });
    }
}
