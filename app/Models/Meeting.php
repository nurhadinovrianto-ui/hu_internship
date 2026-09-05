<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'host_user_id',
        'topic',
        'description',
        'start_time',
        'end_time',
        'status',
        'jitsi_room_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function internships()
    {
        return $this->belongsToMany(Internship::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }
}
