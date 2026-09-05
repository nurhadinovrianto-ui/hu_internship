<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'student_id', 'report_type', 'title', 'file_path',
        'file_size', 'status', 'dpl_feedback', 'reviewed_at', 'reviewed_by',
        'industry_approved_at', 'dpl_approved_at', 'kaprodi_submitted_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'industry_approved_at' => 'datetime',
        'dpl_approved_at' => 'datetime',
        'kaprodi_submitted_at' => 'datetime',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function revisions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FinalReportRevision::class)->orderBy('version', 'desc');
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->report_type === 'industry' 
            ? 'Laporan Proyek / Software (Industri)' 
            : 'Laporan Magang Akademis (Kampus / DPL)';
    }

    public function getStatusBadgeAttribute(): array
    {
        $submittedLabel = $this->report_type === 'industry' ? 'Menunggu Industri' : 'Menunggu DPL';
        return match ($this->status) {
            'submitted' => ['label' => $submittedLabel, 'class' => 'badge-warning'],
            'industry_approved' => ['label' => 'Disetujui Industri', 'class' => 'badge-info'],
            'dpl_approved' => ['label' => 'Disetujui DPL', 'class' => 'badge-primary'],
            'kaprodi_received' => ['label' => 'Diterima Kaprodi', 'class' => 'badge-success'],
            'revision' => ['label' => 'Revisi', 'class' => 'badge-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
