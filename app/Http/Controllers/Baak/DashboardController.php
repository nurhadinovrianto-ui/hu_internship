<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use App\Models\Internship;
use App\Models\GradeConversion;
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

        $stats = [
            'total_students' => Student::forPeriod($periodId)->count(),
            'all_students_count' => Student::count(),
            'sks_eligible' => StudentRequirement::where('sks_eligible', true)->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'sks_pending' => Student::whereDoesntHave('requirements', fn($q) => $periodId ? $q->where('academic_period_id', $periodId) : $q)->count(),
            'grade_pending' => Internship::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'completed')->whereDoesntHave('gradeConversion')->count(),
            'grade_processed' => GradeConversion::when($periodId, fn($q) => $q->whereHas('internship', fn($iq) => $iq->where('academic_period_id', $periodId)))->where('status', 'finalized')->count(),
        ];
        return view('baak.dashboard', compact('stats', 'period', 'periods', 'selectedPeriodId'));
    }
}
