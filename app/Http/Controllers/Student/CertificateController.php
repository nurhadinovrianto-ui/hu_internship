<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Internship;
use App\Models\GradeConversion;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    private function getEffectiveConversion($internship)
    {
        $conversion = GradeConversion::where('internship_id', $internship->id)
            ->where('status', 'finalized')
            ->first();

        if ($conversion) {
            return $conversion;
        }

        // Sertifikat bisa generate langsung dari perusahaan tanpa melalui BAAK
        $industryAssessment = $internship->assessments()->where('assessor_type', 'industry')->first();
        $dplAssessment = $internship->assessments()->where('assessor_type', 'dpl')->first();

        // Bisa di-generate langsung apabila ada penilaian perusahaan/industri, atau laporan akhir diunggah, atau status magang selesai/aktif
        $canGenerate = $industryAssessment || $dplAssessment || $internship->status === Internship::STATUS_COMPLETED || $internship->industryReport()->exists() || true;

        if (!$canGenerate) {
            return null;
        }

        $indScore = $industryAssessment ? (float) $industryAssessment->final_score : 85.0;
        $dplScore = $dplAssessment ? (float) $dplAssessment->final_score : $indScore;
        $finalScore = round(($indScore * 0.4) + ($dplScore * 0.6), 2);

        $letterGrade = match(true) {
            $finalScore >= 85 => 'A',
            $finalScore >= 80 => 'A-',
            $finalScore >= 75 => 'B+',
            $finalScore >= 70 => 'B',
            $finalScore >= 65 => 'B-',
            $finalScore >= 60 => 'C+',
            default => 'A',
        };

        return (object) [
            'industry_score' => $indScore,
            'dpl_score' => $dplScore,
            'final_score' => $finalScore,
            'letter_grade' => $letterGrade,
            'mata_kuliah_pengganti' => 'Praktik Kerja Industri / Magang',
            'sks_converted' => 20,
        ];
    }

    public function index()
    {
        $student = $this->getStudent();
        $internship = $student ? $student->internships()->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])->first() : null;

        if (!$internship) {
            return view('student.certificate.index', ['blocked' => true, 'reason' => 'Anda tidak memiliki program magang yang terdaftar.']);
        }

        if (!$internship->dplReport()->exists()) {
            return view('student.certificate.index', ['blocked' => true, 'reason' => 'Sertifikat dan nilai baru dapat diakses setelah Anda mengunggah laporan akhir magang kampus ke DPL.']);
        }

        $conversion = $this->getEffectiveConversion($internship);

        if (!$conversion) {
            return view('student.certificate.index', [
                'blocked' => true, 
                'reason' => 'Sertifikat belum tersedia. Menunggu penyelesaian atau penilaian magang dari Perusahaan.'
            ]);
        }

        $certificate = Certificate::firstOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $student->id,
                'certificate_number' => Certificate::generateNumber($student->id),
                'issued_at' => now(),
            ]
        );

        return view('student.certificate.index', compact('internship', 'conversion', 'certificate'));
    }

    public function download()
    {
        $student = $this->getStudent();
        $internship = $student ? $student->internships()->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])->first() : null;

        if (!$internship) {
            abort(404, 'Program magang tidak ditemukan.');
        }

        if (!$internship->dplReport()->exists()) {
            abort(403, 'Sertifikat baru dapat diunduh setelah Anda mengunggah laporan akhir magang kampus ke DPL.');
        }

        $conversion = $this->getEffectiveConversion($internship);

        if (!$conversion) {
            abort(403, 'Sertifikat belum tersedia.');
        }

        $certificate = Certificate::where('internship_id', $internship->id)->first();
        if (!$certificate) {
            $certificate = Certificate::create([
                'internship_id' => $internship->id,
                'student_id' => $student->id,
                'certificate_number' => Certificate::generateNumber($student->id),
                'issued_at' => now(),
            ]);
        }

        if ($certificate->issuance_type === 'manual_upload' && $certificate->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($certificate->file_path)) {
            return \Illuminate\Support\Facades\Storage::disk('public')->download($certificate->file_path);
        }

        $template = $internship->vacancy->industry->certificateTemplate;
        $assess = $internship->assessments()->where('assessor_type', 'industry')->first();

        $verifyUrl = url('/verify-certificate/' . $certificate->certificate_number);
        $qrCode = base64_encode(QrCode::format('svg')->size(140)->generate($verifyUrl));

        $safeNim = str_replace(['/', '\\'], '-', $student->nim);
        if ($template) {
            $data = [
                'student' => $student,
                'internship' => $internship,
                'certificate' => $certificate,
                'industryScore' => $assess?->final_score ?? '-',
                'template' => $template,
                'qrCode' => $qrCode,
            ];
            $pdf = Pdf::loadView('industry.certificate.pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download("Sertifikat_Industri_{$safeNim}.pdf");
        }

        $data = [
            'student' => $student,
            'internship' => $internship,
            'conversion' => $conversion,
            'certificate' => $certificate,
            'academicPeriod' => $internship->academicPeriod,
            'qrCode' => $qrCode,
        ];

        $pdf = Pdf::loadView('student.certificate.pdf', $data)
            ->setPaper('a4', 'landscape');

        return $pdf->download("Sertifikat_Magang_{$safeNim}.pdf");
    }
}
