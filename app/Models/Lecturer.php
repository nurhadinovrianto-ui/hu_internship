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
    ];

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
        return $this->dplAssignments()
            ->whereHas('internship', fn($q) => $q->where('status', 'active'))
            ->count();
    }

    public function hasCapacity(): bool
    {
        return $this->current_mentee_count < $this->max_mentee;
    }
}
