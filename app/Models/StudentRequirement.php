<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'academic_period_id',
        'payment_cleared', 'payment_verified_at', 'payment_verified_by',
        'sks_completed', 'sks_minimum', 'sks_eligible',
        'sks_verified_at', 'sks_verified_by', 'notes',
    ];

    protected $casts = [
        'payment_cleared' => 'boolean',
        'sks_eligible' => 'boolean',
        'payment_verified_at' => 'date',
        'sks_verified_at' => 'date',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicPeriod(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function paymentVerifier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function sksVerifier(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'sks_verified_by');
    }

    public function isFullyEligible(): bool
    {
        return $this->payment_cleared && $this->sks_eligible;
    }

    public function getGatekeeperStatusAttribute(): string
    {
        if ($this->isFullyEligible()) return 'eligible';
        if (!$this->payment_cleared && !$this->sks_eligible) return 'both_blocked';
        if (!$this->payment_cleared) return 'payment_blocked';
        return 'sks_blocked';
    }

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($model) {
            // Auto-kalkulasi SKS eligible
            $model->sks_eligible = $model->sks_completed >= $model->sks_minimum;
        });
    }
}
