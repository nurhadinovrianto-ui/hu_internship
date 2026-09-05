<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'study_program_id', 'nip', 'nidn',
        'position', 'specialization', 'max_mentee',
        'cv_file', 'office_room', 'scholar_url', 'sinta_url',
        'linkedin_url', 'bio',
    ];

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_file ? asset('storage/' . $this->cv_file) : null;
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studyProgram(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StudyProgram::class);
    }

    public function dplAssignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DplAssignment::class);
    }

    public function selfProposals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SelfProposedInternship::class, 'dpl_id');
    }

    public function activeInternships(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(
            Internship::class,
            DplAssignment::class,
            'lecturer_id',
            'id',
            'id',
            'internship_id'
        );
    }

    public function getCurrentMenteeCountAttribute(): int
    {
        $periodId = AcademicPeriod::getActive()?->id;
        return $this->dplAssignments()
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->where(function ($q) {
                $q->whereNull('internship_id')
                  ->orWhereHas('internship', fn($iq) => $iq->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_WAITING_DPL]));
            })
            ->count();
    }

    public function getActiveMenteeCountAttribute(): int
    {
        $periodId = AcademicPeriod::getActive()?->id;
        return $this->dplAssignments()
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->whereHas('internship', fn($iq) => $iq->where('status', Internship::STATUS_ACTIVE))
            ->count();
    }

    public function getPrePlacementMenteeCountAttribute(): int
    {
        $periodId = AcademicPeriod::getActive()?->id;
        return $this->dplAssignments()
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->whereNull('internship_id')
            ->count();
    }

    public function hasCapacity(): bool
    {
        return $this->current_mentee_count < $this->max_mentee;
    }
}
