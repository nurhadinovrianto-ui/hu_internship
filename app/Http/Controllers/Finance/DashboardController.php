<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
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

        $stats = [
            'total_students' => Student::forPeriod($periodId)->count(),
            'all_students_count' => Student::count(),
            'payment_cleared' => StudentRequirement::where('payment_cleared', true)
                ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'payment_pending' => StudentRequirement::where('payment_cleared', false)
                ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'not_registered' => Student::whereDoesntHave('requirements', fn($q) =>
                $periodId ? $q->where('academic_period_id', $periodId) : $q
            )->count(),
        ];

        $recentVerified = StudentRequirement::with(['student.user', 'paymentVerifier'])
            ->where('payment_cleared', true)
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->latest()
            ->limit(10)
            ->get();

        return view('finance.dashboard', compact('stats', 'recentVerified', 'period', 'periods', 'selectedPeriodId'));
    }
}
