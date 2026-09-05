<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Industry;
use App\Models\Application;
use App\Models\Internship;
use App\Models\AcademicPeriod;

class DashboardController extends Controller
{
    public function index()
    {
        $period = AcademicPeriod::getActive();

        $stats = [
            'total_users' => User::count(),
            'total_students' => Student::count(),
            'total_industries' => Industry::where('is_active', true)->count(),
            'total_applications' => Application::when($period, fn($q) => $q->where('academic_period_id', $period->id))->count(),
            'active_internships' => Internship::where('status', 'active')->count(),
            'pending_applications' => Application::where('status', 'pending')->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();
        $recentApplications = Application::with(['student.user', 'vacancy.industry'])
            ->latest()
            ->limit(10)
            ->get();

        $applicationStats = [
            'Pending' => Application::where('status', 'pending')->count(),
            'Kaprodi Approved' => Application::where('status', 'kaprodi_approved')->count(),
            'Accepted' => Application::where('status', 'industry_accepted')->count(),
            'Rejected' => Application::whereIn('status', ['kaprodi_rejected', 'industry_rejected'])->count(),
        ];

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentApplications', 'period', 'applicationStats'));
    }
}
