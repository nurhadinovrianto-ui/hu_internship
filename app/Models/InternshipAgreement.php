<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'agreement_number',
        'title',
        'document_file',
        'start_date',
        'end_date',
        'allowance',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function creator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-warning'],
            'active' => ['label' => 'Aktif / Disepakati', 'class' => 'badge-success'],
            'completed' => ['label' => 'Selesai', 'class' => 'badge-info'],
            'terminated' => ['label' => 'Dihentikan', 'class' => 'badge-danger'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
