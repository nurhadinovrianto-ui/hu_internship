<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustrySupervisor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'industry_id', 'position', 'division', 'is_primary'];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function industry(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function vacancies(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Vacancy::class);
    }
}
