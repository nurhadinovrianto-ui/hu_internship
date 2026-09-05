<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MeetingController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $query = Meeting::where('host_user_id', auth()->id())->with('internships.student.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('internships.student.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('internships.student', function ($sq) use ($search) {
                      $sq->where('nim', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $meetings = $query->latest()->paginate(15)->withQueryString();
        return view('supervisor.meetings.index', compact('meetings'));
    }

    public function create()
    {
        $supervisor = $this->getSupervisor();
        $internships = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id)->orWhere('industry_id', $supervisor?->industry_id))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get();

        return view('supervisor.meetings.create', compact('internships'));
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

        $supervisor = $this->getSupervisor();
        $validInternshipIds = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id)->orWhere('industry_id', $supervisor?->industry_id))
            ->whereIn('id', $request->internship_ids)
            ->pluck('id');

        $meeting = Meeting::create([
            'host_user_id' => auth()->id(),
            'topic' => $request->topic,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'status' => 'scheduled',
            'jitsi_room_id' => Str::uuid()->toString(),
        ]);

        $meeting->internships()->sync($validInternshipIds);

        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting berhasil dibuat.');
    }

    public function edit(Meeting $meeting)
    {
        if ($meeting->host_user_id != auth()->id()) {
            abort(403);
        }

        $supervisor = $this->getSupervisor();
        $internships = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id)->orWhere('industry_id', $supervisor?->industry_id))
            ->whereIn('status', ['active'])
            ->with('student.user')
            ->get();

        return view('supervisor.meetings.edit', compact('meeting', 'internships'));
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

        $supervisor = $this->getSupervisor();
        $validInternshipIds = Internship::whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id)->orWhere('industry_id', $supervisor?->industry_id))
            ->whereIn('id', $request->internship_ids)
            ->pluck('id');

        $meeting->update($request->only('topic', 'description', 'start_time', 'status'));
        $meeting->internships()->sync($validInternshipIds);

        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting berhasil diperbarui.');
    }

    public function destroy(Meeting $meeting)
    {
        if ($meeting->host_user_id != auth()->id()) {
            abort(403);
        }
        $meeting->delete();
        return redirect()->route('supervisor.meetings.index')->with('success', 'Jadwal meeting dihapus.');
    }
}
