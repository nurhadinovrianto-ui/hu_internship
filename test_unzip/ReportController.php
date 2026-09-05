<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\FinalReport;
use App\Models\Internship;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private function getActiveInternship()
    {
        $student = auth()->user()->student;
        return $student ? $student->internships()->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])->first() : null;
    }

    public function index()
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return view('student.report.index', ['blocked' => true, 'reason' => 'Anda tidak memiliki program magang aktif.']);
        }

        $report = $internship->finalReport;

        return view('student.report.index', compact('internship', 'report'));
    }

    public function upload(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $maxReportSize = \App\Models\Setting::getValue('max_report_size_kb', 10240);

        $request->validate([
            'title' => 'required|string|max:255',
            'report_file' => "required|file|mimes:pdf|max:{$maxReportSize}",
        ]);

        $file = $request->file('report_file');
        $filePath = $file->store('final_reports', 'public');
        $fileSize = $file->getSize();

        $finalReport = FinalReport::updateOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $internship->student_id,
                'title' => $request->title,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'status' => 'submitted',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'dpl_feedback' => null,
            ]
        );

        // Notification for DPL
        $dpl = $internship->getDpl();
        if ($dpl) {
            $dpl->user->notify(new \App\Notifications\InternshipStatusNotification(
                'Laporan Akhir Diunggah',
                "Mahasiswa {$internship->student->user->name} telah mengunggah Laporan Akhir. Mohon diperiksa dan diberikan penilaian.",
                route('dpl.assessment.index')
            ));
        }

        // Notification for Industry
        $industrySupervisor = $internship->vacancy->supervisor?->user;
        if ($industrySupervisor) {
            $industrySupervisor->notify(new \App\Notifications\InternshipStatusNotification(
                'Laporan Akhir Diunggah',
                "Mahasiswa {$internship->student->user->name} telah mengunggah Laporan Akhir.",
                route('industry.assessment.index')
            ));
        }

        return redirect()->route('student.report.index')->with('success', 'Laporan akhir berhasil diupload. DPL dan Mitra telah dinotifikasi.');
    }
}
