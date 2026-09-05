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

    public function index(Request $request)
    {
        $internshipIds = $this->getInternshipIds();
        $query = Internship::with(['student.user', 'vacancy.industry', 'dplAssessment'])
            ->whereIn('id', $internshipIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy.industry', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'assessed') {
                $query->whereHas('assessments', fn($q) => $q->where('assessor_type', 'dpl'));
            } elseif ($request->status === 'pending') {
                $query->whereDoesntHave('assessments', fn($q) => $q->where('assessor_type', 'dpl'));
            }
        }

        $internships = $query->paginate(15)->withQueryString();

        return view('dpl.assessment.index', compact('internships'));
    }

    public function store(Request $request, Internship $internship)
    {
        $internshipIds = $this->getInternshipIds();
        abort_unless($internshipIds->contains($internship->id), 403, 'Akses ditolak.');

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

}
