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

        $dplReport = $internship->dplReport()->with('revisions.reviewer')->first();
        $industryReport = $internship->industryReport()->with('revisions.reviewer')->first();

        return view('student.report.index', compact('internship', 'dplReport', 'industryReport'));
    }

    public function upload(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $maxReportSize = \App\Models\Setting::getValue('max_report_size_kb', 10240);

        $request->validate([
            'report_type' => 'required|in:dpl,industry',
            'title' => 'required|string|max:255',
            'report_file' => "required|file|mimes:pdf|max:{$maxReportSize}",
            'submitted_at' => 'nullable|date|before_or_equal:today',
        ]);

        $file = $request->file('report_file');
        $filePath = $file->store('final_reports', 'public');
        $fileSize = $file->getSize();

        $submissionDate = $request->filled('submitted_at') ? \Carbon\Carbon::parse($request->submitted_at)->setTime(now()->hour, now()->minute, now()->second) : now();

        $finalReport = FinalReport::updateOrCreate(
            [
                'internship_id' => $internship->id,
                'report_type' => $request->report_type,
            ],
            [
                'student_id' => $internship->student_id,
                'title' => $request->title,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'status' => 'submitted',
                'reviewed_at' => null,
                'reviewed_by' => null,
                'dpl_feedback' => null,
                'created_at' => $submissionDate,
            ]
        );

        $nextVersion = ((int) $finalReport->revisions()->max('version')) + 1;
        $finalReport->revisions()->create([
            'version' => $nextVersion,
            'title' => $request->title,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'status' => 'submitted',
            'created_at' => $submissionDate,
        ]);

        if ($request->report_type === 'dpl') {
            $dpl = $internship->getDpl();
            if ($dpl) {
                $dpl->user->notify(new \App\Notifications\InternshipStatusNotification(
                    'Laporan Akhir Magang (Kampus) Diunggah',
                    "Mahasiswa {$internship->student->user->name} telah mengunggah Laporan Akhir Kampus (Versi {$nextVersion}). Mohon diperiksa.",
                    route('dpl.reports.index')
                ));
            }
        } else {
            $industrySupervisor = $internship->vacancy->supervisor?->user;
            if ($industrySupervisor) {
                $industrySupervisor->notify(new \App\Notifications\InternshipStatusNotification(
                    'Laporan Proyek/Software (Industri) Diunggah',
                    "Mahasiswa {$internship->student->user->name} telah mengunggah Laporan Proyek/Software Industri (Versi {$nextVersion}).",
                    route('industry.reports.index')
                ));
            }
        }

        $typeLabel = $request->report_type === 'dpl' ? 'Laporan Magang Kampus (DPL)' : 'Laporan Proyek / Software (Industri)';
        return redirect()->route('student.report.index')->with('success', "{$typeLabel} Versi {$nextVersion} berhasil diupload.");
    }
}
