<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        $prodi = auth()->user()->managedStudyProgram();
        $query = Internship::with(['student.user', 'vacancy.industry', 'dplAssignment.lecturer.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $internships = $query->latest()->paginate(20)->withQueryString();
        return view('kaprodi.internships.index', compact('internships', 'prodi'));
    }

    public function cancel(Request $request, Internship $internship)
    {
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
