<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
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

        $prodi = auth()->user()->managedStudyProgram();
        $prodiId = $prodi?->id;

        $stats = [
            'pending_applications' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->whereHas('student', fn($q) => $q->where('study_program_id', $prodiId))->where('status', 'pending')->count(),
            'approved_applications' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->whereHas('student', fn($q) => $q->where('study_program_id', $prodiId))->where('status', 'kaprodi_approved')->count(),
            'waiting_dpl' => Internship::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->whereHas('student', fn($q) => $q->where('study_program_id', $prodiId))->where('status', 'waiting_dpl')->count(),
            'active_internships' => Internship::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->whereHas('student', fn($q) => $q->where('study_program_id', $prodiId))->where('status', 'active')->count(),
            'total_students' => Student::forPeriod($periodId)->where('study_program_id', $prodiId)->count(),
            'all_students_count' => Student::where('study_program_id', $prodiId)->count(),
            'available_dpl' => Lecturer::where('study_program_id', $prodiId)->get()->filter(fn($l) => $l->hasCapacity())->count(),
        ];

        $pendingApplications = Application::with(['student.user', 'vacancy.industry'])
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodiId))
            ->where('status', 'pending')
            ->latest()
            ->limit(10)
            ->get();

        return view('kaprodi.dashboard', compact('stats', 'pendingApplications', 'period', 'periods', 'selectedPeriodId', 'prodi'));
    }

    public function statistics()
    {
        $prodi = auth()->user()->managedStudyProgram();
        $internships = Internship::with(['student.user', 'vacancy.industry', 'dplAssignment.lecturer.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))
            ->get();

        $byIndustry = $internships->groupBy(fn($i) => $i->vacancy?->industry?->name ?? 'Tidak Diketahui');
        $byStatus = $internships->groupBy('status');

        // Prepare data for ApexCharts
        $statusLabels = $byStatus->keys()->map(function($status) {
            return match($status) {
                'active' => 'Aktif',
                'completed' => 'Selesai',
                'waiting_dpl' => 'Menunggu DPL',
                'cancelled' => 'Dibatalkan',
                default => $status
            };
        })->toArray();
        $statusSeries = $byStatus->map(fn($group) => $group->count())->values()->toArray();

        $industryLabels = $byIndustry->keys()->toArray();
        $industrySeries = $byIndustry->map(fn($group) => $group->count())->values()->toArray();

        return view('kaprodi.statistics', compact('internships', 'byIndustry', 'byStatus', 'statusLabels', 'statusSeries', 'industryLabels', 'industrySeries', 'prodi'));
    }
}
