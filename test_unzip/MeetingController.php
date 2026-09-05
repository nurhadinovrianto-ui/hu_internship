<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::where('host_user_id', auth()->id())->with('internship.student.user');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15);
        return view('supervisor.meetings.index', compact('meetings'));
    }

    public function create()
    {
        $internships = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', auth()->id()))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get();

        return view('supervisor.meetings.create', compact('internships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'internship_id' => 'required|exists:internships,id',
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after_or_equal:today',
        ]);

        Meeting::create([
            'internship_id' => $request->internship_id,
            'host_user_id' => auth()->id(),
            'topic' => $request->topic,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'status' => 'scheduled',
            'jitsi_room_id' => Str::uuid()->toString(),
        ]);

        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting berhasil dibuat.');
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->host_user_id !== auth()->id()) {
            abort(403);
        }

        $internships = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', auth()->id()))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get();

        return view('supervisor.meetings.edit', compact('meeting', 'internships'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->host_user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'status' => 'required|in:scheduled,active,completed,cancelled',
        ]);

        $meeting->update($request->only('topic', 'description', 'start_time', 'status'));

        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting berhasil diperbarui.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->host_user_id !== auth()->id()) {
            abort(403);
        }
        $meeting->delete();
        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting dihapus.');
    }
}
