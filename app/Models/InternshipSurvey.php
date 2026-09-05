<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternshipSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'internship_id',
        'respondent_id',
        'respondent_type',
        'q1_rating',
        'q2_rating',
        'q3_rating',
        'q4_rating',
        'q5_rating',
        'overall_score',
        'feedback_text',
        'recommendation',
    ];

    protected $casts = [
        'q1_rating' => 'integer',
        'q2_rating' => 'integer',
        'q3_rating' => 'integer',
        'q4_rating' => 'integer',
        'q5_rating' => 'integer',
        'overall_score' => 'decimal:2',
        'recommendation' => 'boolean',
    ];

    public function internship(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function respondent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    // Helper untuk pertanyaan kuesioner berdasarkan peran
    public static function getQuestions(string $type): array
    {
        if ($type === 'industry') {
            return [
                'q1' => 'Integritas & Etika Kerja (Kedisiplinan, kejujuran, dan sopan santun mahasiswa di tempat kerja)',
                'q2' => 'Keahlian Teknis (Hard skills / kompetensi keilmuan mahasiswa dalam menyelesaikan pekerjaan)',
                'q3' => 'Komunikasi & Kerjasama Tim (Soft skills / kemampuan berkoordinasi dan berinteraksi)',
                'q4' => 'Inisiatif & Pemecahan Masalah (Kreativitas dan respon terhadap tantangan pekerjaan)',
                'q5' => 'Kesiapan Kerja Keseluruhan (Tingkat kesiapan mahasiswa memasuki dunia profesional)',
            ];
        }

        return [
            'q1' => 'Kesesuaian Jobdesk (Kesesuaian tugas magang dengan bidang studi / kurikulum perkuliahan)',
            'q2' => 'Bimbingan Mentor Industri (Perhatian, arahan, dan transfer ilmu yang diberikan supervisor)',
            'q3' => 'Lingkungan & Budaya Kerja (Kenyamanan, keamanan, dan perlakuan adil di perusahaan mitra)',
            'q4' => 'Fasilitas & Dukungan (Dukungan alat kerja, akses ruang, atau tunjangan/uang saku jika ada)',
            'q5' => 'Pengembangan Diri (Peningkatan wawasan, keterampilan, dan portofolio yang didapatkan)',
        ];
    }
}
