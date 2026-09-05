<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $prodi = auth()->user()->managedStudyProgram();
        $query = Application::with(['student.user', 'vacancy.industry'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy', function ($vq) use ($search) {
                    $vq->where('position', 'like', "%{$search}%")
                       ->orWhere('title', 'like', "%{$search}%")
                       ->orWhereHas('industry', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $applications = $query->paginate(20)->withQueryString();
        return view('kaprodi.applications.index', compact('applications', 'prodi'));
    }

    public function show(Application $application)
    {
        $prodi = auth()->user()->managedStudyProgram();
        abort_unless($application->student->study_program_id == $prodi?->id, 403, 'Anda tidak memiliki akses ke data aplikasi ini.');

        $application->load(['student.user', 'student.studyProgram', 'vacancy.industry', 'vacancy.supervisor.user']);
        return view('kaprodi.applications.show', compact('application'));
    }

    public function approve(Request $request, Application $application)
    {
        $prodi = auth()->user()->managedStudyProgram();
        abort_unless($application->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate(['notes' => 'nullable|string|max:500']);

        if ($application->status !== Application::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $application->update([
            'status' => Application::STATUS_KAPRODI_APPROVED,
            'kaprodi_notes' => $request->notes,
            'kaprodi_reviewed_at' => now(),
            'kaprodi_reviewed_by' => auth()->id(),
        ]);

        $application->student->user->notify(new InternshipStatusNotification(
            'Lamaran Disetujui Kaprodi',
            'Lamaran Anda untuk posisi ' . $application->vacancy->position . ' telah disetujui Kaprodi dan sedang direview oleh Industri.',
            route('student.applications.show', $application->id)
        ));

        // Notify Industry Supervisor
        $supervisor = $application->vacancy->supervisor?->user;
        if ($supervisor) {
            $supervisor->notify(new InternshipStatusNotification(
                'Lamaran Baru',
                'Ada lamaran magang baru dari ' . $application->student->user->name . ' untuk posisi ' . $application->vacancy->position . ' yang telah disetujui Kaprodi.',
                route('industry.agreements.index')
            ));
        }

        return back()->with('success', 'Pengajuan berhasil disetujui. Menunggu seleksi industri.');
    }

    public function reject(Request $request, Application $application)
    {
        $prodi = auth()->user()->managedStudyProgram();
        abort_unless($application->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate(['notes' => 'required|string|max:500']);

        if ($application->status !== Application::STATUS_PENDING) {
            return back()->with('error', 'Pengajuan ini sudah diproses.');
        }

        $application->update([
            'status' => Application::STATUS_KAPRODI_REJECTED,
            'kaprodi_notes' => $request->notes,
            'kaprodi_reviewed_at' => now(),
            'kaprodi_reviewed_by' => auth()->id(),
        ]);

        $application->student->user->notify(new InternshipStatusNotification(
            'Lamaran Ditolak Kaprodi',
            'Lamaran Anda untuk posisi ' . $application->vacancy->position . ' telah ditolak oleh Kaprodi.',
            route('student.applications.show', $application->id)
        ));

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}
