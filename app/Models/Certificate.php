<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id', 'student_id', 'certificate_number', 'file_path', 'issued_at', 'issued_by',
    ];

    protected $casts = ['issued_at' => 'datetime'];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function getDownloadUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return route('student.certificate.download');
        }
        return null;
    }

    public static function generateNumber(int $studentId): string
    {
        $year = now()->year;
        $seq = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('CERT/%d/%04d/%05d', $year, $studentId, $seq);
    }
}
