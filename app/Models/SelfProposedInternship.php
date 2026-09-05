<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfProposedInternship extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_period_id',
        'dpl_id',
        'company_name',
        'industry_sector',
        'company_address',
        'latitude',
        'longitude',
        'geofence_radius',
        'contact_person_name',
        'contact_person_position',
        'contact_person_email',
        'contact_person_phone',
        'position_title',
        'job_description',
        'start_date',
        'end_date',
        'loa_file_path',
        'status',
        'dpl_status',
        'dpl_notes',
        'dpl_reviewed_at',
        'kaprodi_notes',
        'reviewed_by',
        'reviewed_at',
        'internship_id',
        'partner_user_id',
        'partner_temp_password',
        'partner_account_created',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reviewed_at' => 'datetime',
        'dpl_reviewed_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'geofence_radius' => 'integer',
        'partner_account_created' => 'boolean',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicPeriod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function dpl(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'dpl_id');
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function partnerUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'partner_user_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'submitted' => ['label' => 'Menunggu Review DPL', 'class' => 'badge-warning'],
            'dpl_approved' => ['label' => 'Disetujui DPL (Menunggu Kaprodi)', 'class' => 'badge-info'],
            'under_review' => ['label' => 'Sedang Direview', 'class' => 'badge-info'],
            'revision' => ['label' => 'Perlu Revisi', 'class' => 'badge-secondary'],
            'approved' => ['label' => 'Disetujui (Magang Aktif)', 'class' => 'badge-success'],
            'rejected' => ['label' => 'Ditolak', 'class' => 'badge-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }

    public function getDplStatusBadgeAttribute(): array
    {
        return match ($this->dpl_status) {
            'approved' => ['label' => 'Disetujui DPL', 'class' => 'badge-success'],
            'revision' => ['label' => 'Perlu Revisi', 'class' => 'badge-warning'],
            'rejected' => ['label' => 'Ditolak DPL', 'class' => 'badge-danger'],
            default => ['label' => 'Menunggu Review DPL', 'class' => 'badge-secondary'],
        };
    }
}
