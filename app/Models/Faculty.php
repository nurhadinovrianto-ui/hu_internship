<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faculty extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'dean_name', 'dean_user_id', 'description', 'status'];

    public function studyPrograms(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StudyProgram::class);
    }

    public function dean(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'dean_user_id');
    }

    public function getActiveStudyProgramsCountAttribute(): int
    {
        return $this->studyPrograms()->where('status', 'active')->count();
    }
}
