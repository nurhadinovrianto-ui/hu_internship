<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'study_program_id', 'nim', 'batch', 'current_semester',
        'total_sks', 'gpa', 'address', 'emergency_contact', 'date_of_birth',
        'gender', 'photo', 'cv_file', 'transcript_file', 'portfolio_url',
        'linkedin_url', 'github_url', 'skills', 'bio',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gpa' => 'decimal:2',
        'total_sks' => 'integer',
    ];

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_file ? asset('storage/' . $this->cv_file) : null;
    }

    public function getTranscriptUrlAttribute(): ?string
    {
        return $this->transcript_file ? asset('storage/' . $this->transcript_file) : null;
    }

    public function getSkillsArrayAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        return array_values(array_filter(array_map('trim', explode(',', $this->skills))));
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function requirements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentRequirement::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function internships(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Internship::class);
    }

    public function dplAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DplAssignment::class);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function certificates(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(StudentLocation::class);
    }

    public function locationLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentLocationLog::class);
    }

    // Cek apakah mahasiswa eligible (gatekeeper)
    public function getRequirementForPeriod(int $periodId): ?StudentRequirement
    {
        return $this->requirements()->where('academic_period_id', $periodId)->first();
    }

    public function isEligibleForInternship(?int $periodId = null): bool
    {
        $period = $periodId ?? AcademicPeriod::getActive()?->id;
        if (!$period) return false;

        $req = $this->getRequirementForPeriod($period);
        return $req && $req->payment_cleared && $req->sks_eligible;
    }

    public function getActiveInternship(): ?Internship
    {
        return $this->internships()->where('status', 'active')->first();
    }

    public function getDplForPeriod(?int $periodId = null): ?Lecturer
    {
        $period = $periodId ?? AcademicPeriod::getActive()?->id;
        if (!$period) return null;

        // 1. Cek jika sudah magang aktif dengan DPL
        $activeInternship = $this->internships()
            ->where('academic_period_id', $period)
            ->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_WAITING_DPL])
            ->first();

        if ($activeInternship && $activeInternship->dplAssignment?->lecturer) {
            return $activeInternship->dplAssignment->lecturer;
        }

        // 2. Cek penugasan DPL pra-penempatan (belum ditempatkan)
        $preAssignment = $this->dplAssignments()
            ->where('academic_period_id', $period)
            ->latest()
            ->first();

        return $preAssignment?->lecturer;
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return $this->user?->getAvatarUrlAttribute() ?? asset('edumin/images/avatar/1.jpg');
    }

    public function scopeForPeriod($query, ?int $periodId)
    {
        if (!$periodId) return $query;
        return $query->where(function ($q) use ($periodId) {
            $q->whereHas('requirements', fn($r) => $r->where('academic_period_id', $periodId))
              ->orWhereHas('applications', fn($a) => $a->where('academic_period_id', $periodId))
              ->orWhereHas('internships', fn($i) => $i->where('academic_period_id', $periodId));
        });
    }

    public function selfProposals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SelfProposedInternship::class);
    }

    public function defenses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InternshipDefense::class);
    }
}
