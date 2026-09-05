<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'student_id', 'date',
        'check_in_time', 'check_in_lat', 'check_in_lng', 'check_in_photo',
        'check_out_time', 'check_out_lat', 'check_out_lng',
        'work_duration_minutes', 'status', 'notes',
        'location_type', 'approval_status', 'approved_by'
    ];

    protected $casts = ['date' => 'date'];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getWorkDurationFormattedAttribute(): string
    {
        if (!$this->work_duration_minutes) return '-';
        $h = intdiv($this->work_duration_minutes, 60);
        $m = $this->work_duration_minutes % 60;
        return "{$h}j {$m}m";
    }

    public function getStatusBadgeAttribute(): array
    {
        if ($this->approval_status === 'pending') {
            return ['label' => 'Menunggu Approval', 'class' => 'badge-warning'];
        } elseif ($this->approval_status === 'rejected') {
            return ['label' => 'Ditolak', 'class' => 'badge-danger'];
        }

        return match($this->status) {
            'present' => ['label' => 'Hadir', 'class' => 'badge-success'],
            'absent' => ['label' => 'Tidak Hadir', 'class' => 'badge-danger'],
            'permission' => ['label' => 'Izin', 'class' => 'badge-info'],
            'sick' => ['label' => 'Sakit', 'class' => 'badge-warning'],
            default => ['label' => ucfirst($this->status), 'class' => 'badge-light'],
        };
    }
}
