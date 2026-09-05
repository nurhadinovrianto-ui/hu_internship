<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LetterController extends Controller
{
    public function downloadPengantar(Application $application)
    {
        // Pastikan hanya bisa didownload jika statusnya sudah di-approve Kaprodi atau di-acc Industri
        if (!in_array($application->status, [Application::STATUS_KAPRODI_APPROVED, Application::STATUS_INDUSTRY_ACCEPTED, Application::STATUS_INDUSTRY_REJECTED])) {
            abort(403, 'Surat pengantar magang belum bisa diunduh. Menunggu persetujuan Kaprodi.');
        }

        // Jika user adalah mahasiswa, pastikan itu lamarannya
        if (auth()->user()->hasRole('mahasiswa')) {
            $student = auth()->user()->student;
            abort_unless($student && $application->student_id == $student->id, 403);
        } elseif (auth()->user()->hasRole('kaprodi')) {
            $studyProgram = auth()->user()->managedStudyProgram();
            abort_unless($studyProgram && $application->student?->study_program_id == $studyProgram->id, 403);
        }

        $application->load(['student.user', 'student.studyProgram.faculty', 'vacancy.industry', 'academicPeriod', 'kaprodiReviewer.lecturer']);

        $data = [
            'application' => $application,
            'student' => $application->student,
            'industry' => $application->vacancy?->industry,
            'date' => Carbon::parse($application->kaprodi_reviewed_at ?? now())->translatedFormat('d F Y'),
        ];

        $pdf = Pdf::loadView('student.letters.pengantar-pdf', $data)
            ->setPaper('a4', 'portrait');

        $nim = str_replace(['/', '\\'], '-', $application->student?->nim ?? 'NIM');
        return $pdf->download("Surat_Pengantar_Magang_{$nim}.pdf");
    }
}
