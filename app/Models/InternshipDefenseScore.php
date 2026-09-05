<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipDefenseScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'defense_id',
        'evaluator_id',
        'evaluator_role',
        'presentation_score',
        'material_mastery_score',
        'report_quality_score',
        'average_score',
        'notes',
    ];

    protected $casts = [
        'presentation_score' => 'decimal:2',
        'material_mastery_score' => 'decimal:2',
        'report_quality_score' => 'decimal:2',
        'average_score' => 'decimal:2',
    ];

    public function defense(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InternshipDefense::class, 'defense_id');
    }

    public function evaluator(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public static function calculateAverage(float $presentation, float $mastery, float $report): float
    {
        // Komposisi penilaian sidang: 30% Presentasi, 40% Penguasaan Materi, 30% Kualitas Laporan
        return round(($presentation * 0.30) + ($mastery * 0.40) + ($report * 0.30), 2);
    }
}
