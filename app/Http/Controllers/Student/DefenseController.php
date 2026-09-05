<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipDefense;
use Illuminate\Http\Request;

class DefenseController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    public function index()
    {
        $student = $this->getStudent();
        $internship = $student->internships()
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->first();

        if (!$internship) {
            return view('student.defense.no-internship');
        }

        $defense = InternshipDefense::with(['examiner.user', 'supervisor.user', 'scores.evaluator'])
            ->where('internship_id', $internship->id)
            ->first();

        // Cek apakah laporan akhir sudah diunggah
        $finalReport = $internship->finalReports()->latest()->first();

        return view('student.defense.index', compact('student', 'internship', 'defense', 'finalReport'));
    }

    public function register(Request $request)
    {
        $student = $this->getStudent();
        $internship = $student->internships()
            ->whereIn('status', ['active', 'completed'])
            ->latest()
            ->firstOrFail();

        $existing = InternshipDefense::where('internship_id', $internship->id)->first();
        if ($existing && in_array($existing->status, ['registered', 'scheduled', 'passed'])) {
            return back()->with('error', 'Anda sudah mendaftar sidang ujian magang.');
        }

        $request->validate([
            'presentation_file' => 'required|file|mimes:pdf,ppt,pptx|max:10240',
            'clearance_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $presentationPath = $request->file('presentation_file')->store('defense_presentations', 'public');
        $clearancePath = $request->hasFile('clearance_file') 
            ? $request->file('clearance_file')->store('defense_clearances', 'public') 
            : null;

        $dplLecturerId = $internship->dplAssignment?->lecturer_id;

        InternshipDefense::updateOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $student->id,
                'presentation_file_path' => $presentationPath,
                'clearance_file_path' => $clearancePath,
                'status' => 'registered',
                'supervisor_lecturer_id' => $dplLecturerId,
            ]
        );

        return redirect()->route('student.defense.index')
            ->with('success', 'Pendaftaran sidang ujian magang berhasil! Menunggu penetapan jadwal dan dosen penguji oleh Kaprodi.');
    }
}
