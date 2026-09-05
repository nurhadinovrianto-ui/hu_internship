<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DplAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'lecturer_id', 'assigned_by', 'assigned_at', 'notes',
    ];

    protected $casts = ['assigned_at' => 'datetime'];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function lecturer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lecturer::class);
    }

    public function assignedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
