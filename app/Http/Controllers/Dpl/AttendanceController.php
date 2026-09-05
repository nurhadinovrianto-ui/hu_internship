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

        if ($request->search) {
            $query->whereHas('student.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->date) {
            $query->where('date', $request->date);
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
