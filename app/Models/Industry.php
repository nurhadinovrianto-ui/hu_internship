<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Industry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'industry_type', 'address', 'city', 'province',
        'website', 'email', 'phone', 'contact_person', 'description', 'logo',
        'partnership_status', 'mou_start_date', 'mou_end_date', 'mou_document', 'is_active',
    ];

    protected $casts = [
        'mou_start_date' => 'date',
        'mou_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function supervisors(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IndustrySupervisor::class);
    }

    public function vacancies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vacancy::class);
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return asset('edumin/images/avatar/2.jpg');
    }

    public function getActiveVacanciesCountAttribute(): int
    {
        return $this->vacancies()
            ->where('is_published', true)
            ->where('is_closed', false)
            ->where('apply_deadline', '>=', now()->toDateString())
            ->count();
    }

    public function hasMou(): bool
    {
        return $this->partnership_status !== 'none' &&
               $this->mou_end_date &&
               $this->mou_end_date >= now()->toDateString();
    }

    public function certificateTemplate()
    {
        return $this->hasOne(IndustryCertificateTemplate::class);
    }
}
