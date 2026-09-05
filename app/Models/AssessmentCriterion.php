<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCriterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'weight',
        'assessor_type',
        'industry_id',
        'academic_period_id',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function scores()
    {
        return $this->hasMany(AssessmentScore::class);
    }

    /**
     * Dapatkan daftar kriteria aktif untuk industri tertentu
     */
    public static function getForIndustry(?int $industryId = null)
    {
        if ($industryId) {
            $custom = self::where('assessor_type', 'industry')
                ->where('industry_id', $industryId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($custom->isNotEmpty()) {
                return $custom;
            }
        }

        // Kriteria default umum untuk industri (industry_id = null)
        return self::where('assessor_type', 'industry')
            ->whereNull('industry_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Dapatkan daftar kriteria aktif untuk DPL berdasarkan periode magang
     */
    public static function getForDpl(?int $academicPeriodId = null)
    {
        if ($academicPeriodId) {
            $custom = self::where('assessor_type', 'dpl')
                ->where('academic_period_id', $academicPeriodId)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($custom->isNotEmpty()) {
                return $custom;
            }
        }

        // Kriteria default umum untuk DPL
        return self::where('assessor_type', 'dpl')
            ->whereNull('academic_period_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
