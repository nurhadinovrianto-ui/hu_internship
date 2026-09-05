<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'year', 'semester', 'start_date', 'end_date',
        'apply_start', 'apply_end', 'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'apply_start' => 'date',
        'apply_end' => 'date',
        'is_active' => 'boolean',
    ];

    public function studentRequirements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudentRequirement::class);
    }

    public function vacancies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Application::class);
    }

    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }

    public function isApplicationOpen(): bool
    {
        if (!$this->apply_start || !$this->apply_end) return false;
        $now = now()->toDateString();
        return $now >= $this->apply_start->toDateString() && $now <= $this->apply_end->toDateString();
    }
}
