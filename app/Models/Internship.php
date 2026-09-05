<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id', 'student_id', 'vacancy_id', 'academic_period_id',
        'status', 'start_date', 'end_date', 'actual_end_date', 'termination_reason',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_end_date' => 'date',
    ];

    const STATUS_WAITING_DPL = 'waiting_dpl';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_TERMINATED = 'terminated';

    public function application(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function vacancy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }

    public function academicPeriod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function dplAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(DplAssignment::class);
    }

    public function attendances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Logbook::class);
    }

    public function meetings(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Meeting::class);
    }

    public function finalReport(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FinalReport::class);
    }

    public function finalReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FinalReport::class);
    }

    public function dplReport(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FinalReport::class)->where('report_type', 'dpl');
    }

    public function industryReport(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FinalReport::class)->where('report_type', 'industry');
    }

    public function assessments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function industryAssessment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assessment::class)->where('assessor_type', 'industry');
    }

    public function dplAssessment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assessment::class)->where('assessor_type', 'dpl');
    }

    public function gradeConversion(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(GradeConversion::class);
    }

    public function certificate(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Certificate::class);
    }

    public function agreement(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(InternshipAgreement::class);
    }

    public function getDplAssessmentAttribute(): ?Assessment
    {
        return $this->assessments()->where('assessor_type', 'dpl')->first();
    }

    public function getIndustryAssessmentAttribute(): ?Assessment
    {
        return $this->assessments()->where('assessor_type', 'industry')->first();
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            self::STATUS_WAITING_DPL => ['label' => 'Menunggu DPL', 'class' => 'badge-warning'],
            self::STATUS_ACTIVE => ['label' => 'Aktif', 'class' => 'badge-success'],
            self::STATUS_COMPLETED => ['label' => 'Selesai', 'class' => 'badge-info'],
            self::STATUS_TERMINATED => ['label' => 'Dihentikan', 'class' => 'badge-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }

    public function getProgressPercentageAttribute(): int
    {
        if (!$this->start_date || !$this->end_date) return 0;
        $total = $this->start_date->diffInDays($this->end_date);
        if ($total === 0) return 0;
        $elapsed = $this->start_date->diffInDays(now()->toDateString());
        return min(100, round(($elapsed / $total) * 100));
    }

    public function getDpl(): ?Lecturer
    {
        return $this->dplAssignment?->lecturer;
    }
}
