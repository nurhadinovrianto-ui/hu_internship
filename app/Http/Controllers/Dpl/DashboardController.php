<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\DplAssignment;
use App\Models\Internship;
use App\Models\Logbook;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    public function index(Request $request)
    {
        $lecturer = $this->getLecturer();

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

        $assignments = DplAssignment::with([
            'student.user',
            'student.studyProgram',
            'student.applications.vacancy.industry',
            'internship.student.user',
            'internship.vacancy.industry',
        ])
        ->where('lecturer_id', $lecturer?->id)
        ->when($periodId, function ($q) use ($periodId) {
            $q->where(function ($sq) use ($periodId) {
                $sq->where('academic_period_id', $periodId)
                   ->orWhereHas('internship', fn($iq) => $iq->where('academic_period_id', $periodId));
            });
        })
        ->get();

        $activeInternships = $assignments->filter(fn($a) => $a->internship?->status === 'active');
        $prePlacementAssignments = $assignments->filter(fn($a) => is_null($a->internship_id));

        $stats = [
            'total_students' => $assignments->count(),
            'active_students' => $activeInternships->count(),
            'pre_placement_students' => $prePlacementAssignments->count(),
            'pending_logbooks' => Logbook::whereIn('internship_id', $activeInternships->pluck('internship_id'))
                ->where('status', 'submitted')->count(),
        ];

        $pendingLogbooks = Logbook::with(['student.user', 'internship.vacancy'])
            ->whereIn('internship_id', $activeInternships->pluck('internship_id'))
            ->where('status', 'submitted')
            ->latest()
            ->limit(10)
            ->get();

        return view('dpl.dashboard', compact('stats', 'assignments', 'prePlacementAssignments', 'pendingLogbooks', 'period', 'periods', 'selectedPeriodId'));
    }

    public function students(Request $request)
    {
        $lecturer = $this->getLecturer();
        $query = DplAssignment::with([
            'student.user',
            'student.studyProgram',
            'student.applications.vacancy.industry',
            'internship.student.user',
            'internship.vacancy.industry',
            'internship.attendances',
        ])
        ->where('lecturer_id', $lecturer?->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('internship.student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('internship.vacancy.industry', function ($ind) use ($search) {
                    $ind->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'pre_placement') {
                $query->whereNull('internship_id');
            } elseif ($request->status === 'active') {
                $query->whereHas('internship', fn($iq) => $iq->where('status', 'active'));
            } elseif ($request->status === 'completed') {
                $query->whereHas('internship', fn($iq) => $iq->where('status', 'completed'));
            }
        }

        $assignments = $query->latest()->paginate(15)->withQueryString();

        return view('dpl.students', compact('assignments'));
    }
}
