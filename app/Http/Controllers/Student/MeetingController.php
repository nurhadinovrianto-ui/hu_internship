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
        $internship = Internship::where('student_id', auth()->user()->student->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (!$internship) {
            $meetings = collect();
            return view('student.meetings.index', compact('meetings'));
        }

        $query = Meeting::whereHas('internships', function($q) use ($internship) {
            $q->where('internships.id', $internship->id);
        })->with('host');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15);
        return view('student.meetings.index', compact('meetings'));
    }
}
