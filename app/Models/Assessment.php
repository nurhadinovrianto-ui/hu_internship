<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'assessor_id', 'assessor_type',
        'report_score', 'presentation_score', 'logbook_score',
        'discipline_score', 'skill_score', 'attitude_score', 'teamwork_score', 'initiative_score',
        'final_score', 'feedback', 'assessed_at',
    ];

    protected $casts = [
        'assessed_at' => 'datetime',
        'final_score' => 'decimal:2',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function assessor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function scores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AssessmentScore::class);
    }

    // Kalkulasi nilai akhir otomatis (untuk DPL)
    public function calculateDplScore(): float
    {
        $scores = array_filter([
            $this->report_score * 0.40,
            $this->presentation_score * 0.30,
            $this->logbook_score * 0.30,
        ]);
        return round(array_sum($scores), 2);
    }

    // Kalkulasi nilai akhir otomatis (untuk Industri)
    public function calculateIndustryScore(): float
    {
        $scores = array_filter([
            $this->discipline_score * 0.20,
            $this->skill_score * 0.30,
            $this->attitude_score * 0.20,
            $this->teamwork_score * 0.15,
            $this->initiative_score * 0.15,
        ]);
        return round(array_sum($scores), 2);
    }

    public function getLetterGrade(): string
    {
        return match(true) {
            $this->final_score >= 85 => 'A',
            $this->final_score >= 80 => 'A-',
            $this->final_score >= 75 => 'B+',
            $this->final_score >= 70 => 'B',
            $this->final_score >= 65 => 'B-',
            $this->final_score >= 60 => 'C+',
            $this->final_score >= 55 => 'C',
            $this->final_score >= 50 => 'D',
            default => 'E',
        };
    }
}
