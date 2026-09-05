<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\Internship;
use App\Models\FinalReport;
use App\Models\DplAssignment;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    private function getInternshipIds()
    {
        $lecturer = auth()->user()->lecturer;
        return DplAssignment::where('lecturer_id', $lecturer?->id)->pluck('internship_id');
    }

    public function index()
    {
        $internshipIds = $this->getInternshipIds();
        $internships = Internship::with(['student.user', 'vacancy.industry', 'dplAssessment'])
            ->whereIn('id', $internshipIds)
            ->get();

        return view('dpl.assessment.index', compact('internships'));
    }

    public function store(Request $request, Internship $internship)
    {
        $validated = $request->validate([
            'report_score' => 'required|numeric|min:0|max:100',
            'presentation_score' => 'required|numeric|min:0|max:100',
            'logbook_score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $finalScore = ($validated['report_score'] * 0.40) +
                      ($validated['presentation_score'] * 0.30) +
                      ($validated['logbook_score'] * 0.30);

        Assessment::updateOrCreate(
            ['internship_id' => $internship->id, 'assessor_type' => 'dpl'],
            array_merge($validated, [
                'assessor_id' => auth()->id(),
                'final_score' => round($finalScore, 2),
                'assessed_at' => now(),
            ])
        );

        // Jika kedua penilaian (DPL dan Industri) sudah ada, tandai internship selesai
        $hasIndustryAssessment = $internship->assessments()->where('assessor_type', 'industry')->exists();
        if ($hasIndustryAssessment) {
            $internship->update(['status' => Internship::STATUS_COMPLETED]);
        }

        return back()->with('success', 'Penilaian akademik berhasil disimpan.');
    }

    public function reports()
    {
        $internshipIds = $this->getInternshipIds();
        $reports = FinalReport::with(['student.user', 'internship.vacancy.industry'])
            ->whereIn('internship_id', $internshipIds)
            ->latest()
            ->get();

        return view('dpl.reports', compact('reports'));
    }
}
