<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use App\Models\Internship;
use App\Models\GradeConversion;

class DashboardController extends Controller
{
    public function index()
    {
        $period = AcademicPeriod::getActive();
        $stats = [
            'sks_eligible' => StudentRequirement::where('sks_eligible', true)->when($period, fn($q) => $q->where('academic_period_id', $period->id))->count(),
            'sks_pending' => Student::whereDoesntHave('requirements', fn($q) => $period ? $q->where('academic_period_id', $period->id) : $q)->count(),
            'grade_pending' => GradeConversion::where('status', 'pending')->count(),
            'grade_processed' => GradeConversion::where('status', 'finalized')->count(),
        ];
        return view('baak.dashboard', compact('stats', 'period'));
    }
}
