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

    public function index(Request $request, $vacancyId)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            abort(403, 'Akses ditolak.');
        }

        $vacancy = \App\Models\Vacancy::findOrFail($vacancyId);
        abort_unless($vacancy->industry_id == $supervisor->industry_id || $vacancy->industry_supervisor_id == $supervisor->id, 403, 'Akses ditolak.');

        $query = Application::with(['student.user', 'student.studyProgram', 'vacancy'])
            ->where('vacancy_id', $vacancyId)
            ->where('status', Application::STATUS_KAPRODI_APPROVED);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nim', 'like', "%{$search}%")
                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                   ->orWhereHas('studyProgram', fn($sp) => $sp->where('name', 'like', "%{$search}%"));
            });
        }

        $applicants = $query->latest()->paginate(15)->withQueryString();

        return view('industry.applicants.index', compact('applicants', 'vacancyId'));
    }

    public function accept(Request $request, Application $application)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            abort(403, 'Akses ditolak.');
        }

        abort_unless($application->vacancy->industry_id == $supervisor->industry_id || $application->vacancy->industry_supervisor_id == $supervisor->id, 403, 'Akses ditolak.');

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

        // Cek apakah mahasiswa sudah memiliki DPL bimbingan pra-penempatan
        $preAssignment = \App\Models\DplAssignment::where('student_id', $application->student_id)
            ->where('academic_period_id', $application->academic_period_id)
            ->whereNull('internship_id')
            ->first();

        $internshipStatus = $preAssignment ? Internship::STATUS_ACTIVE : Internship::STATUS_WAITING_DPL;

        // Auto-create internship record
        $internship = Internship::create([
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'vacancy_id' => $application->vacancy_id,
            'academic_period_id' => $application->academic_period_id,
            'status' => $internshipStatus,
            'start_date' => $vacancy->start_date ?? now()->toDateString(),
        ]);

        if ($preAssignment) {
            $preAssignment->update(['internship_id' => $internship->id]);

            // Notify DPL
            $preAssignment->lecturer->user->notify(new InternshipStatusNotification(
                'Mahasiswa Bimbingan Diterima Magang',
                "Mahasiswa bimbingan Anda {$application->student->user->name} telah resmi diterima di {$vacancy->industry->name} ({$vacancy->position}). Magang kini berstatus Aktif.",
                route('dpl.students')
            ));
        }

        // Auto-cancel other active applications for this student
        Application::where('student_id', $application->student_id)
            ->where('id', '!=', $application->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])
            ->update([
                'status' => 'cancelled_by_system',
                'industry_notes' => 'Otomatis dibatalkan oleh sistem karena mahasiswa telah diterima di lowongan lain.',
            ]);

        // Jika kuota habis setelah accept ini, tutup lowongan
        if ($vacancy->fresh()->remaining_quota <= 0) {
            $vacancy->update(['is_closed' => true]);
        }

        $dplText = $preAssignment
            ? " DPL Anda ({$preAssignment->lecturer->user->name}) yang telah ditetapkan sebelumnya otomatis terhubung dan magang Anda kini Aktif."
            : " Menunggu Kaprodi menugaskan DPL.";

        $application->student->user->notify(new InternshipStatusNotification(
            'Selamat! Anda Diterima Magang',
            'Lamaran Anda untuk posisi ' . $vacancy->position . ' di ' . $vacancy->industry->name . ' telah diterima.' . $dplText,
            route('student.applications.show', $application->id)
        ));

        $feedbackMsg = $preAssignment
            ? 'Pelamar berhasil diterima magang. Dosen DPL yang ditetapkan di awal otomatis terhubung dan status magang langsung Aktif.'
            : 'Pelamar berhasil diterima magang. Selanjutnya Kaprodi akan memplot DPL.';

        return back()->with('success', $feedbackMsg);
    }

    public function reject(Request $request, Application $application)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            abort(403, 'Akses ditolak.');
        }

        abort_unless($application->vacancy->industry_id == $supervisor->industry_id || $application->vacancy->industry_supervisor_id == $supervisor->id, 403, 'Akses ditolak.');

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
