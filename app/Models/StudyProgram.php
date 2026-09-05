<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudyProgram extends Model
{
    use HasFactory;

    protected $fillable = ['faculty_id', 'name', 'code', 'head_name', 'head_user_id', 'degree', 'status'];

    public function faculty(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function head(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function students(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function lecturers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lecturer::class);
    }
}
