<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class StudentLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'internship_id',
        'latitude',
        'longitude',
        'accuracy',
        'speed',
        'heading',
        'battery_level',
        'status',
        'last_ping_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'float',
        'speed' => 'float',
        'heading' => 'float',
        'battery_level' => 'integer',
        'last_ping_at' => 'datetime',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    /**
     * Tentukan apakah lokasi masih aktif / online (ping dalam 2 menit terakhir).
     */
    public function getIsOnlineAttribute(): bool
    {
        if (!$this->last_ping_at) {
            return false;
        }

        return $this->last_ping_at->greaterThanOrEqualTo(now()->subMinutes(2));
    }

    /**
     * Format teks waktu relatif pembaruan terakhir.
     */
    public function getLastSeenFormattedAttribute(): string
    {
        if (!$this->last_ping_at) {
            return 'Belum pernah terlacak';
        }

        return $this->last_ping_at->diffForHumans();
    }
}
