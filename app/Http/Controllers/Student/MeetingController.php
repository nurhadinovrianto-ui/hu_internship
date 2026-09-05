<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Internship;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        // Student can only see meetings for their active internship
        $student = auth()->user()->student;
        if (!$student) {
            return redirect()->route('auth.pending');
        }

        $internship = Internship::where('student_id', $student->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (!$internship) {
            $meetings = Meeting::whereRaw('1 = 0')->paginate(15);
            return view('student.meetings.index', compact('meetings'));
        }

        $query = Meeting::whereHas('internships', function($q) use ($internship) {
            $q->where('internships.id', $internship->id);
        })->with('host');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('host', fn($hq) => $hq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15)->withQueryString();
        return view('student.meetings.index', compact('meetings'));
    }
}
