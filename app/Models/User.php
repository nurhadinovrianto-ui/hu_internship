<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, Impersonate;

    protected $fillable = [
        'name', 'email', 'password', 'google_id', 'avatar', 'phone', 'status',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function student(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function lecturer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Lecturer::class);
    }

    public function industrySupervisor(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(IndustrySupervisor::class);
    }

    // Helper method
    public function getDashboardRoute(): string
    {
        if ($this->hasRole('super-admin')) return route('admin.dashboard');
        if ($this->hasRole('finance')) return route('finance.dashboard');
        if ($this->hasRole('baak')) return route('baak.dashboard');
        if ($this->hasRole('kaprodi')) return route('kaprodi.dashboard');
        if ($this->hasRole('dekan')) return route('dekan.dashboard');
        if ($this->hasRole('dpl')) return route('dpl.dashboard');
        if ($this->hasRole('supervisor-industri')) return route('industry.dashboard');
        if ($this->hasRole('mahasiswa')) return route('student.dashboard');
        return route('auth.pending');
    }

    public function getRoleLabel(): string
    {
        $labels = [
            'super-admin' => 'Super Admin',
            'finance' => 'Finance',
            'baak' => 'BAAK',
            'kaprodi' => 'Kaprodi',
            'dekan' => 'Dekan',
            'dpl' => 'Dosen Pembimbing',
            'supervisor-industri' => 'Supervisor Industri',
            'mahasiswa' => 'Mahasiswa',
        ];
        $role = $this->getRoleNames()->first();
        return $labels[$role] ?? ucfirst($role ?? 'User');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return str_starts_with($this->avatar, 'http') ? $this->avatar : asset('storage/' . $this->avatar);
        }
        return asset('edumin/images/avatar/1.jpg');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function managedStudyProgram(): ?StudyProgram
    {
        $prodi = StudyProgram::where('head_user_id', $this->id)->first();
        if ($prodi) return $prodi;

        if ($this->lecturer && $this->lecturer->studyProgram) {
            return $this->lecturer->studyProgram;
        }

        if ($this->hasRole('kaprodi')) {
            return StudyProgram::where('code', 'TI')->first() ?? StudyProgram::first();
        }

        return null;
    }

    public function managedFaculty(): ?Faculty
    {
        $faculty = Faculty::where('dean_user_id', $this->id)->first();
        if ($faculty) return $faculty;

        if ($this->lecturer && $this->lecturer->studyProgram && $this->lecturer->studyProgram->faculty) {
            return $this->lecturer->studyProgram->faculty;
        }

        $prodi = $this->managedStudyProgram();
        if ($prodi && $prodi->faculty) {
            return $prodi->faculty;
        }

        if ($this->hasRole('dekan')) {
            return Faculty::where('code', 'FICT')->first() ?? Faculty::first();
        }

        return null;
    }

    public function canImpersonate(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('super-admin');
    }
}
