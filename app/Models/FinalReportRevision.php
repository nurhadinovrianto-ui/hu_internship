<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalReportRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'final_report_id', 'version', 'title', 'file_path',
        'file_size', 'status', 'feedback', 'reviewed_by', 'reviewed_at'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function finalReport(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinalReport::class);
    }

    public function reviewer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'submitted' => ['label' => 'Diunggah', 'class' => 'badge-warning'],
            'industry_approved' => ['label' => 'Disetujui Industri', 'class' => 'badge-info'],
            'dpl_approved' => ['label' => 'Disetujui DPL', 'class' => 'badge-primary'],
            'kaprodi_received' => ['label' => 'Diterima Kaprodi', 'class' => 'badge-success'],
            'revision' => ['label' => 'Minta Revisi', 'class' => 'badge-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
