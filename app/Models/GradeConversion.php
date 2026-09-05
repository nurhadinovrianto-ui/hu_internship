<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeConversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'student_id',
        'industry_score', 'dpl_score', 'final_score',
        'letter_grade', 'grade_point', 'sks_converted', 'mata_kuliah_pengganti',
        'processed_by', 'processed_at', 'status',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'final_score' => 'decimal:2',
        'grade_point' => 'decimal:2',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function processor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Kalkulasi nilai akhir gabungan (Industri 40% + DPL 60%)
    public function calculateFinalScore(): float
    {
        $industryWeight = 0.40;
        $dplWeight = 0.60;
        return round(
            ($this->industry_score * $industryWeight) + ($this->dpl_score * $dplWeight),
            2
        );
    }
}
