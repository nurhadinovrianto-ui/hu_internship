<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\Lecturer;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        $prodi = auth()->user()->managedStudyProgram();
        $query = Internship::with(['student.user', 'vacancy.industry', 'dplAssignment.lecturer.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy.industry', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                })->orWhereHas('dplAssignment.lecturer.user', function ($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $internships = $query->latest()->paginate(20)->withQueryString();

        $baseQuery = Internship::whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'waiting_dpl' => (clone $baseQuery)->where('status', Internship::STATUS_WAITING_DPL)->count(),
            'active' => (clone $baseQuery)->where('status', Internship::STATUS_ACTIVE)->count(),
            'completed' => (clone $baseQuery)->where('status', Internship::STATUS_COMPLETED)->count(),
        ];

        $lecturers = Lecturer::with('user')->get()->map(fn($l) => [
            'id' => $l->id,
            'name' => $l->user->name,
            'current_mentee' => $l->current_mentee_count,
            'max_mentee' => $l->max_mentee,
            'has_capacity' => $l->hasCapacity(),
        ]);

        return view('kaprodi.internships.index', compact('internships', 'prodi', 'stats', 'lecturers'));
    }

    public function cancel(Request $request, Internship $internship)
    {
        $prodi = auth()->user()->managedStudyProgram();
        abort_unless($internship->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        if (in_array($internship->status, ['completed', 'terminated'])) {
            return back()->with('error', 'Status magang ini sudah tidak dapat diubah.');
        }

        $internship->update([
            'status' => 'terminated',
            'termination_reason' => $request->reason,
            'actual_end_date' => now()->toDateString(),
        ]);

        // Fix Quota Leak: Batalkan juga lamaran agar sisa kuota industri kembali
        if ($internship->application) {
            $internship->application->update([
                'status' => \App\Models\Application::STATUS_CANCELLED,
                'kaprodi_notes' => 'Dibatalkan karena magang dihentikan (Terminated): ' . $request->reason,
            ]);

            // Jika lowongan sebelumnya ditutup karena penuh, dan sekarang kuota > 0, buka kembali
            $vacancy = $internship->vacancy;
            if ($vacancy && $vacancy->is_closed && $vacancy->remaining_quota > 0) {
                $vacancy->update(['is_closed' => false]);
            }
        }

        // Notify Student
        if ($internship->student && $internship->student->user) {
            $internship->student->user->notify(new InternshipStatusNotification(
                'Program Magang Dibatalkan',
                'Program magang Anda di ' . $internship->vacancy->industry->name . ' telah dibatalkan oleh Kaprodi dengan alasan: ' . $request->reason,
                '#'
            ));
        }

        return back()->with('success', 'Program magang berhasil dibatalkan.');
    }
}
