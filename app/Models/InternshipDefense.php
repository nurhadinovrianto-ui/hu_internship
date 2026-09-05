<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipDefense extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'student_id',
        'presentation_file_path',
        'clearance_file_path',
        'status',
        'scheduled_date',
        'start_time',
        'end_time',
        'room_or_link',
        'examiner_lecturer_id',
        'supervisor_lecturer_id',
        'final_score',
        'grade_letter',
        'revision_notes',
        'revision_deadline',
        'passed_at',
        'official_report_number',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'revision_deadline' => 'date',
        'passed_at' => 'datetime',
        'final_score' => 'decimal:2',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examiner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'examiner_lecturer_id');
    }

    public function supervisor(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'supervisor_lecturer_id');
    }

    public function scores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InternshipDefenseScore::class, 'defense_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'registered' => ['label' => 'Terdaftar (Menunggu Jadwal)', 'class' => 'badge-warning'],
            'scheduled' => ['label' => 'Terjadwal', 'class' => 'badge-info'],
            'completed' => ['label' => 'Selesai Sidang', 'class' => 'badge-primary'],
            'passed' => ['label' => 'Lulus Sidang', 'class' => 'badge-success'],
            'revision' => ['label' => 'Perlu Revisi Sidang', 'class' => 'badge-danger'],
            'failed' => ['label' => 'Tidak Lulus', 'class' => 'badge-dark'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
