<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\DplAssignment;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function getInternshipIds()
    {
        $lecturer = auth()->user()->lecturer;
        return DplAssignment::where('lecturer_id', $lecturer?->id)->pluck('internship_id');
    }

    public function index(Request $request)
    {
        $internshipIds = $this->getInternshipIds();
        
        $query = Attendance::with(['student.user', 'internship.vacancy.industry'])
            ->whereIn('internship_id', $internshipIds);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nim', 'like', "%{$search}%")
                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }
        
        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_status')) {
            $query->where('approval_status', $request->approval_status);
        }

        $attendances = $query->latest('date')->paginate(20)->withQueryString();

        return view('dpl.attendance.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        $internshipIds = $this->getInternshipIds();
        abort_unless($internshipIds->contains($attendance->internship_id), 403);

        $attendance->load(['student.user', 'internship.vacancy.industry']);

        return view('dpl.attendance.show', compact('attendance'));
    }
}
