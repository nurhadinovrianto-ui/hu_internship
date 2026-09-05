<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\DplAssignment;
use App\Models\Internship;
use App\Models\Logbook;

class DashboardController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    public function index()
    {
        $lecturer = $this->getLecturer();

        $assignments = DplAssignment::with(['internship.student.user', 'internship.vacancy.industry'])
            ->where('lecturer_id', $lecturer?->id)
            ->get();

        $activeInternships = $assignments->filter(fn($a) => $a->internship->status === 'active');

        $stats = [
            'total_students' => $assignments->count(),
            'active_students' => $activeInternships->count(),
            'pending_logbooks' => Logbook::whereIn('internship_id', $activeInternships->pluck('internship_id'))
                ->where('status', 'submitted')->count(),
        ];

        $pendingLogbooks = Logbook::with(['student.user', 'internship.vacancy'])
            ->whereIn('internship_id', $activeInternships->pluck('internship_id'))
            ->where('status', 'submitted')
            ->latest()
            ->limit(10)
            ->get();

        return view('dpl.dashboard', compact('stats', 'assignments', 'pendingLogbooks'));
    }

    public function students()
    {
        $lecturer = $this->getLecturer();
        $assignments = DplAssignment::with(['internship.student.user', 'internship.vacancy.industry', 'internship.attendances'])
            ->where('lecturer_id', $lecturer?->id)
            ->get();

        return view('dpl.students', compact('assignments'));
    }
}
