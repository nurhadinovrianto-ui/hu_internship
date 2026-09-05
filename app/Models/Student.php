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
        'gender', 'photo',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gpa' => 'decimal:2',
        'total_sks' => 'integer',
    ];

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

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return $this->user?->getAvatarUrlAttribute() ?? asset('edumin/images/avatar/1.jpg');
    }
}
