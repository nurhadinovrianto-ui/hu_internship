<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vacancy extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'industry_id', 'industry_supervisor_id', 'academic_period_id',
        'title', 'position', 'division', 'description', 'requirements',
        'quota', 'duration_months', 'start_date', 'apply_deadline',
        'work_type', 'location', 'is_published', 'is_closed',
    ];

    protected $casts = [
        'start_date' => 'date',
        'apply_deadline' => 'date',
        'is_published' => 'boolean',
        'is_closed' => 'boolean',
        'quota' => 'integer',
        'duration_months' => 'integer',
    ];

    public function industry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function supervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(IndustrySupervisor::class, 'industry_supervisor_id');
    }

    public function academicPeriod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function getAcceptedCountAttribute(): int
    {
        return $this->applications()->where('status', 'industry_accepted')->count();
    }

    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->quota - $this->accepted_count);
    }

    public function isOpen(): bool
    {
        return $this->is_published &&
               !$this->is_closed &&
               $this->apply_deadline >= now()->toDateString() &&
               $this->remaining_quota > 0;
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->is_closed) return ['label' => 'Ditutup', 'class' => 'badge-danger'];
        if ($this->remaining_quota <= 0) return ['label' => 'Penuh', 'class' => 'badge-warning'];
        if ($this->apply_deadline < now()->toDateString()) return ['label' => 'Kadaluarsa', 'class' => 'badge-secondary'];
        if (!$this->is_published) return ['label' => 'Draft', 'class' => 'badge-info'];
        return ['label' => 'Buka', 'class' => 'badge-success'];
    }

    public function getWorkTypeLabelAttribute(): string
    {
        return match($this->work_type) {
            'onsite' => 'Onsite',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            default => ucfirst($this->work_type),
        };
    }
}
