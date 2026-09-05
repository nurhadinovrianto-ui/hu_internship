<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Internship;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class ApplicantController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index($vacancyId)
    {
        $supervisor = $this->getSupervisor();
        $applicants = Application::with(['student.user', 'student.studyProgram', 'vacancy'])
            ->where('vacancy_id', $vacancyId)
            ->where('status', Application::STATUS_KAPRODI_APPROVED)
            ->latest()
            ->paginate(15);

        return view('industry.applicants.index', compact('applicants', 'vacancyId'));
    }

    public function accept(Request $request, Application $application)
    {
        $request->validate(['notes' => 'nullable|string|max:500']);

        if ($application->status !== Application::STATUS_KAPRODI_APPROVED) {
            return back()->with('error', 'Pelamar tidak dapat diproses.');
        }

        // Cek kuota lowongan
        $vacancy = $application->vacancy;
        if ($vacancy->remaining_quota <= 0) {
            return back()->with('error', 'Kuota lowongan magang sudah penuh.');
        }

        $application->update([
            'status' => Application::STATUS_INDUSTRY_ACCEPTED,
            'industry_notes' => $request->notes,
            'industry_reviewed_at' => now(),
        ]);

        // Auto-create internship record (Waiting DPL plotting)
        Internship::create([
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'vacancy_id' => $application->vacancy_id,
            'academic_period_id' => $application->academic_period_id,
            'status' => Internship::STATUS_WAITING_DPL,
        ]);

        // Auto-cancel other active applications for this student
        Application::where('student_id', $application->student_id)
            ->where('id', '!=', $application->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])
            ->update([
                'status' => 'cancelled_by_system',
                'industry_notes' => 'Otomatis dibatalkan oleh sistem karena mahasiswa telah diterima di lowongan lain.',
            ]);

        // Jika kuota habis setelah accept ini, tutup lowongan
        if ($vacancy->remaining_quota <= 0) {
            $vacancy->update(['is_closed' => true]);
        }

        $application->student->user->notify(new InternshipStatusNotification(
            'Selamat! Anda Diterima Magang',
            'Lamaran Anda untuk posisi ' . $vacancy->position . ' di ' . $vacancy->industry->name . ' telah diterima. Menunggu Kaprodi menugaskan DPL.',
            route('student.applications.show', $application->id)
        ));

        return back()->with('success', 'Pelamar berhasil diterima magang. Selanjutnya Kaprodi akan memplot DPL.');
    }

    public function reject(Request $request, Application $application)
    {
        $request->validate(['notes' => 'required|string|max:500']);

        if ($application->status !== Application::STATUS_KAPRODI_APPROVED) {
            return back()->with('error', 'Pelamar tidak dapat diproses.');
        }

        $application->update([
            'status' => Application::STATUS_INDUSTRY_REJECTED,
            'industry_notes' => $request->notes,
            'industry_reviewed_at' => now(),
        ]);

        $application->student->user->notify(new InternshipStatusNotification(
            'Maaf, Lamaran Anda Ditolak Industri',
            'Lamaran Anda untuk posisi ' . $application->vacancy->position . ' di ' . $application->vacancy->industry->name . ' telah ditolak.',
            route('student.applications.show', $application->id)
        ));

        return back()->with('success', 'Pelamar berhasil ditolak.');
    }
}
