<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\Internship;
use App\Models\Assessment;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();

        $periods = AcademicPeriod::orderByDesc('start_date')->get();
        $selectedPeriodId = $request->get('period_id');
        if ($selectedPeriodId === 'all') {
            $period = null;
        } elseif ($selectedPeriodId) {
            $period = AcademicPeriod::find($selectedPeriodId) ?? AcademicPeriod::getActive();
            $selectedPeriodId = $period?->id;
        } else {
            $period = AcademicPeriod::getActive();
            $selectedPeriodId = $period?->id;
        }
        $periodId = $period?->id;

        $stats = [
            'total_vacancies' => Vacancy::where('industry_supervisor_id', $supervisor?->id)->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'active_vacancies' => Vacancy::where('industry_supervisor_id', $supervisor?->id)->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('is_published', true)->where('is_closed', false)->count(),
            'total_applicants' => Application::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id))->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'pending_applicants' => Application::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id))->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'kaprodi_approved')->count(),
            'active_interns' => Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id))->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'active')->count(),
        ];

        $recentApplications = Application::with(['student.user', 'vacancy'])
            ->whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id))
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->where('status', 'kaprodi_approved')
            ->latest()
            ->limit(10)
            ->get();

        return view('industry.dashboard', compact('stats', 'recentApplications', 'period', 'periods', 'selectedPeriodId'));
    }

    public function assessment(Request $request)
    {
        $supervisor = $this->getSupervisor();
        $query = Internship::with(['student.user', 'student.studyProgram', 'vacancy', 'assessments'])
            ->whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id)->orWhere('industry_id', $supervisor?->industry_id))
            ->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status_industry')) {
            if ($request->status_industry === 'assessed') {
                $query->whereHas('assessments', fn($q) => $q->where('assessor_type', 'industry'));
            } elseif ($request->status_industry === 'pending') {
                $query->whereDoesntHave('assessments', fn($q) => $q->where('assessor_type', 'industry'));
            }
        }

        if ($request->filled('status_dpl')) {
            if ($request->status_dpl === 'assessed') {
                $query->whereHas('assessments', fn($q) => $q->where('assessor_type', 'dpl'));
            } elseif ($request->status_dpl === 'pending') {
                $query->whereDoesntHave('assessments', fn($q) => $q->where('assessor_type', 'dpl'));
            }
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        $internships = $query->latest()->paginate(10)->withQueryString();
        $criteria = \App\Models\AssessmentCriterion::getForIndustry($supervisor?->industry_id);
        $academicPeriods = \App\Models\AcademicPeriod::orderBy('start_date', 'desc')->get();

        return view('industry.assessment.index', compact('internships', 'criteria', 'academicPeriods'));
    }

    public function storeAssessment(Request $request, Internship $internship)
    {
        $supervisor = $this->getSupervisor();
        $isAllowed = $supervisor && (
            $internship->vacancy?->industry_supervisor_id == $supervisor->id ||
            ($supervisor->industry_id && $internship->vacancy?->industry_id == $supervisor->industry_id)
        );
        abort_unless($isAllowed, 403, 'Akses ditolak.');

        $criteria = \App\Models\AssessmentCriterion::getForIndustry($supervisor->industry_id);

        $validated = $request->validate([
            'scores' => 'nullable|array',
            'scores.*' => 'numeric|min:0|max:100',
            'discipline_score' => 'nullable|numeric|min:0|max:100',
            'skill_score' => 'nullable|numeric|min:0|max:100',
            'attitude_score' => 'nullable|numeric|min:0|max:100',
            'teamwork_score' => 'nullable|numeric|min:0|max:100',
            'initiative_score' => 'nullable|numeric|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $finalScore = 0;

        if ($request->filled('scores') && is_array($request->scores)) {
            $totalWeighted = 0;
            $totalWeight = 0;
            foreach ($criteria as $criterion) {
                $scoreVal = floatval($request->scores[$criterion->id] ?? 0);
                $totalWeighted += $scoreVal * ($criterion->weight / 100);
                $totalWeight += ($criterion->weight / 100);
            }
            $finalScore = $totalWeight > 0 ? ($totalWeighted / $totalWeight) : 0;
        } else {
            $disc = floatval($validated['discipline_score'] ?? 0);
            $skill = floatval($validated['skill_score'] ?? 0);
            $att = floatval($validated['attitude_score'] ?? 0);
            $team = floatval($validated['teamwork_score'] ?? 0);
            $init = floatval($validated['initiative_score'] ?? 0);

            $finalScore = ($disc * 0.20) + ($skill * 0.30) + ($att * 0.20) + ($team * 0.15) + ($init * 0.15);
        }

        $assessment = Assessment::updateOrCreate(
            ['internship_id' => $internship->id, 'assessor_type' => 'industry'],
            [
                'assessor_id' => auth()->id(),
                'discipline_score' => $validated['discipline_score'] ?? 0,
                'skill_score' => $validated['skill_score'] ?? 0,
                'attitude_score' => $validated['attitude_score'] ?? 0,
                'teamwork_score' => $validated['teamwork_score'] ?? 0,
                'initiative_score' => $validated['initiative_score'] ?? 0,
                'feedback' => $validated['feedback'] ?? null,
                'final_score' => round($finalScore, 2),
                'assessed_at' => now(),
            ]
        );

        if ($request->filled('scores') && is_array($request->scores)) {
            foreach ($request->scores as $critId => $scoreValue) {
                \App\Models\AssessmentScore::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'assessment_criterion_id' => $critId],
                    ['score' => floatval($scoreValue)]
                );
            }
        }

        // Jika kedua penilaian (DPL dan Industri) sudah ada, tandai internship selesai
        $hasDplAssessment = $internship->assessments()->where('assessor_type', 'dpl')->exists();
        if ($hasDplAssessment) {
            $internship->update(['status' => Internship::STATUS_COMPLETED]);
        }

        return back()->with('success', 'Penilaian industri berhasil disimpan.');
    }
}
