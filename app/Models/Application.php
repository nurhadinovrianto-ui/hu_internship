<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'vacancy_id', 'academic_period_id', 'status',
        'kaprodi_notes', 'kaprodi_reviewed_at', 'kaprodi_reviewed_by',
        'industry_notes', 'industry_reviewed_at',
        'cv_file', 'motivation_letter', 'cover_letter',
    ];

    protected $casts = [
        'kaprodi_reviewed_at' => 'datetime',
        'industry_reviewed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_KAPRODI_APPROVED = 'kaprodi_approved';
    const STATUS_KAPRODI_REJECTED = 'kaprodi_rejected';
    const STATUS_INDUSTRY_ACCEPTED = 'industry_accepted';
    const STATUS_INDUSTRY_REJECTED = 'industry_rejected';
    const STATUS_CANCELLED = 'cancelled';

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

    public function kaprodiReviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'kaprodi_reviewed_by');
    }

    public function internship(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Internship::class);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            self::STATUS_PENDING => ['label' => 'Menunggu Kaprodi', 'class' => 'badge-warning'],
            self::STATUS_KAPRODI_APPROVED => ['label' => 'Disetujui Kaprodi', 'class' => 'badge-info'],
            self::STATUS_KAPRODI_REJECTED => ['label' => 'Ditolak Kaprodi', 'class' => 'badge-danger'],
            self::STATUS_INDUSTRY_ACCEPTED => ['label' => 'Diterima Industri', 'class' => 'badge-success'],
            self::STATUS_INDUSTRY_REJECTED => ['label' => 'Ditolak Industri', 'class' => 'badge-danger'],
            self::STATUS_CANCELLED => ['label' => 'Dibatalkan', 'class' => 'badge-secondary'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }

    public function canReApply(): bool
    {
        return in_array($this->status, [self::STATUS_KAPRODI_REJECTED, self::STATUS_INDUSTRY_REJECTED]);
    }
}
