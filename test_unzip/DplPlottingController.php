<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\Lecturer;
use App\Models\DplAssignment;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class DplPlottingController extends Controller
{
    public function index()
    {
        $prodi = auth()->user()->managedStudyProgram();
        $internships = Internship::with(['student.user', 'vacancy.industry', 'dplAssignment.lecturer.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))
            ->where('status', 'waiting_dpl')
            ->latest()
            ->get();

        $lecturersQuery = Lecturer::with('user');

        $lecturers = $lecturersQuery->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'name' => $l->user->name,
                'current_mentee' => $l->current_mentee_count,
                'max_mentee' => $l->max_mentee,
                'has_capacity' => $l->hasCapacity(),
            ]);

        return view('kaprodi.dpl-plotting.index', compact('internships', 'lecturers', 'prodi'));
    }

    public function assign(Request $request, Internship $internship)
    {
        $request->validate(['lecturer_id' => 'required|exists:lecturers,id']);

        $lecturer = Lecturer::findOrFail($request->lecturer_id);

        if (!$lecturer->hasCapacity()) {
            return back()->with('error', "Dosen {$lecturer->user->name} sudah mencapai batas maksimal bimbingan.");
        }

        // Hapus assignment lama jika ada
        $internship->dplAssignment?->delete();

        DplAssignment::create([
            'internship_id' => $internship->id,
            'lecturer_id' => $request->lecturer_id,
            'assigned_by' => auth()->id(),
            'assigned_at' => now(),
            'notes' => $request->notes,
        ]);

        // Update status internship menjadi aktif
        $internship->update([
            'status' => Internship::STATUS_ACTIVE,
            'start_date' => $internship->start_date ?? now()->toDateString(),
        ]);

        // Notify Student
        $internship->student->user->notify(new InternshipStatusNotification(
            'DPL Telah Ditugaskan',
            "Bapak/Ibu {$lecturer->user->name} telah ditugaskan sebagai DPL Anda untuk magang di {$internship->vacancy->industry->name}. Program magang Anda kini berstatus Aktif.",
            route('student.dashboard')
        ));

        // Notify DPL
        $lecturer->user->notify(new InternshipStatusNotification(
            'Penugasan Bimbingan Baru',
            "Anda ditugaskan membimbing mahasiswa {$internship->student->user->name} yang magang di {$internship->vacancy->industry->name}.",
            route('dpl.students')
        ));

        return back()->with('success', "DPL {$lecturer->user->name} berhasil ditugaskan. Magang sekarang aktif.");
    }
}
