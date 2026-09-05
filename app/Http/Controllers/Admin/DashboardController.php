<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Industry;
use App\Models\Application;
use App\Models\Internship;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $periods = AcademicPeriod::orderByDesc('start_date')->get();

        // Tentukan periode yang dipilih (default: periode aktif)
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
            'total_users' => User::count(),
            'total_students' => Student::forPeriod($periodId)->count(),
            'all_students_count' => Student::count(),
            'total_industries' => Industry::where('is_active', true)->count(),
            'total_applications' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->count(),
            'active_internships' => Internship::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'active')->count(),
            'pending_applications' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentApplications = Application::with(['student.user', 'vacancy.industry'])
            ->when($periodId, fn($q) => $q->where('academic_period_id', $periodId))
            ->latest()
            ->limit(10)
            ->get();

        $applicationStats = [
            'Pending' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'pending')->count(),
            'Kaprodi Approved' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'kaprodi_approved')->count(),
            'Accepted' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->where('status', 'industry_accepted')->count(),
            'Rejected' => Application::when($periodId, fn($q) => $q->where('academic_period_id', $periodId))->whereIn('status', ['kaprodi_rejected', 'industry_rejected'])->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentApplications', 'period', 'periods', 'selectedPeriodId', 'applicationStats'));
    }
}
