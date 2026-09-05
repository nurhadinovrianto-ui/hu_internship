<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $period = AcademicPeriod::getActive();

        $stats = [
            'total_students' => Student::count(),
            'payment_cleared' => StudentRequirement::where('payment_cleared', true)
                ->when($period, fn($q) => $q->where('academic_period_id', $period->id))->count(),
            'payment_pending' => StudentRequirement::where('payment_cleared', false)
                ->when($period, fn($q) => $q->where('academic_period_id', $period->id))->count(),
            'not_registered' => Student::whereDoesntHave('requirements', fn($q) =>
                $period ? $q->where('academic_period_id', $period->id) : $q
            )->count(),
        ];

        $recentVerified = StudentRequirement::with(['student.user', 'paymentVerifier'])
            ->where('payment_cleared', true)
            ->when($period, fn($q) => $q->where('academic_period_id', $period->id))
            ->latest()
            ->limit(10)
            ->get();

        return view('finance.dashboard', compact('stats', 'recentVerified', 'period'));
    }
}
