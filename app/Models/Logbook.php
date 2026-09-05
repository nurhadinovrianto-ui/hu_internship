<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logbook extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'internship_id', 'student_id', 'date',
        'title', 'description', 'learning_outcomes', 'attachment', 'status',
    ];

    protected $casts = ['date' => 'date'];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(LogbookReview::class);
    }

    public function dplReview(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LogbookReview::class)->where('reviewer_type', 'dpl');
    }

    public function industryReview(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(LogbookReview::class)->where('reviewer_type', 'industry');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'draft' => ['label' => 'Draft', 'class' => 'badge-secondary'],
            'submitted' => ['label' => 'Terkirim', 'class' => 'badge-info'],
            'reviewed_dpl' => ['label' => 'Direview DPL', 'class' => 'badge-primary'],
            'reviewed_industry' => ['label' => 'Direview Industri', 'class' => 'badge-success'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
