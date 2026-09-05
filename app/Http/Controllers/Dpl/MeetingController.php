<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $query = Meeting::where('host_user_id', auth()->id())->with('internships.student.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('internships.student.user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15)->withQueryString();
        return view('dpl.meetings.index', compact('meetings'));
    }

    public function create()
    {
        $lecturer = auth()->user()->lecturer;
        if (!$lecturer) {
            return redirect()->route('dpl.meetings.index')->with('error', 'Profil Dosen Anda belum terhubung.');
        }

        $internships = Internship::whereHas('dplAssignment', fn($q) => $q->where('lecturer_id', $lecturer->id))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get();

        return view('dpl.meetings.create', compact('internships'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'internship_ids' => 'required|array|min:1',
            'internship_ids.*' => 'exists:internships,id',
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after_or_equal:today',
        ]);

        $meeting = Meeting::create([
            'host_user_id' => auth()->id(),
            'topic' => $request->topic,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'status' => 'scheduled',
            'jitsi_room_id' => Str::uuid()->toString(),
        ]);

        $meeting->internships()->sync($request->internship_ids);

        return redirect()->route('dpl.meetings.index')->with('success', 'Jadwal meeting berhasil dibuat.');
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->host_user_id != auth()->id()) {
            abort(403);
        }

        $lecturer = auth()->user()->lecturer;
        $internships = $lecturer ? Internship::whereHas('dplAssignment', fn($q) => $q->where('lecturer_id', $lecturer->id))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get() : collect();

        return view('dpl.meetings.edit', compact('meeting', 'internships'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        if ($meeting->host_user_id != auth()->id()) {
            abort(403);
        }

        $request->validate([
            'internship_ids' => 'required|array|min:1',
            'internship_ids.*' => 'exists:internships,id',
            'topic' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'status' => 'required|in:scheduled,active,completed,cancelled',
        ]);

        $meeting->update($request->only('topic', 'description', 'start_time', 'status'));
        $meeting->internships()->sync($request->internship_ids);

        return redirect()->route('dpl.meetings.index')->with('success', 'Jadwal meeting berhasil diperbarui.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->host_user_id != auth()->id()) {
            abort(403);
        }
        $meeting->delete();
        return redirect()->route('dpl.meetings.index')->with('success', 'Jadwal meeting dihapus.');
    }
}
