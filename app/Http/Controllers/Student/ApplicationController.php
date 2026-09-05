<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\Vacancy;
use App\Models\Application;
use App\Models\User;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    public function browse(Request $request)
    {
        $student = $this->getStudent();
        $period = AcademicPeriod::getActive();

        if (!$period) {
            return view('student.vacancies.browse', ['vacancies' => collect(), 'blocked' => true, 'reason' => 'Tidak ada periode magang aktif.']);
        }

        // Cek Gatekeeper (Finance & BAAK)
        $req = $student->getRequirementForPeriod($period->id);
        if (!$req || !$req->isFullyEligible()) {
            $reason = 'Anda tidak memenuhi syarat administrasi/akademik untuk periode ini.';
            if ($req) {
                if (!$req->payment_cleared && !$req->sks_eligible) {
                    $reason = 'Pembayaran SPP Anda belum divalidasi oleh Finance DAN jumlah SKS Anda belum mencukupi batas minimum.';
                } elseif (!$req->payment_cleared) {
                    $reason = 'Pembayaran SPP Anda belum divalidasi oleh Finance.';
                } elseif (!$req->sks_eligible) {
                    $reason = 'Jumlah SKS Anda belum mencukupi batas minimum (' . $req->sks_minimum . ' SKS).';
                }
            } else {
                $reason = 'Data verifikasi SKS dan pembayaran Anda belum diinput oleh BAAK/Finance.';
            }
            return view('student.vacancies.browse', ['vacancies' => collect(), 'blocked' => true, 'reason' => $reason]);
        }

        // Cek Syarat Minimal Akademik (Otomatis)
        if ($student->total_sks < 90 || (float)$student->gpa < 2.50) {
            $reason = "Syarat Akademik Tidak Terpenuhi: Anda harus memiliki minimal 90 SKS (SKS saat ini: {$student->total_sks}) dan IPK di atas 2.50 (IPK saat ini: {$student->gpa}) untuk dapat melamar program magang.";
            return view('student.vacancies.browse', ['vacancies' => collect(), 'blocked' => true, 'reason' => $reason]);
        }

        // Mahasiswa tidak boleh apply jika sudah ada magang aktif atau lamaran aktif yang disetujui
        $hasActiveInternship = $student->internships()->whereIn('status', ['active', 'waiting_dpl'])->exists();
        if ($hasActiveInternship) {
            return view('student.vacancies.browse', ['vacancies' => collect(), 'blocked' => true, 'reason' => 'Anda sudah memiliki program magang yang sedang berjalan atau menunggu penugasan DPL.']);
        }

        $query = Vacancy::with(['industry'])
            ->where('academic_period_id', $period->id)
            ->where('is_published', true)
            ->where('is_closed', false)
            ->where('apply_deadline', '>=', now()->toDateString());

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('position', 'like', "%{$request->search}%")
                  ->orWhereHas('industry', fn($ind) => $ind->where('name', 'like', "%{$request->search}%"));
            });
        }

        $vacancies = $query->paginate(12)->withQueryString();

        return view('student.vacancies.browse', compact('vacancies', 'period'));
    }

    public function showVacancy(Vacancy $vacancy)
    {
        $vacancy->load('industry');
        return view('student.vacancies.show', compact('vacancy'));
    }

    public function apply(Request $request, Vacancy $vacancy)
    {
        $student = $this->getStudent();
        $period = AcademicPeriod::getActive();

        if (!$period || ((int) $vacancy->academic_period_id) !== ((int) $period->id)) {
            return back()->with('error', 'Lowongan ini tidak tersedia untuk periode aktif.');
        }

        // Validasi gatekeeper ulang
        if (!$student->isEligibleForInternship($period->id)) {
            return back()->with('error', 'Anda tidak memenuhi syarat administrasi.');
        }

        if ($student->total_sks < 90 || (float)$student->gpa < 2.50) {
            return back()->with('error', 'Syarat akademik tidak terpenuhi (Min 90 SKS & IPK 2.50).');
        }

        // Cek apakah sudah pernah apply ke lowongan ini dan statusnya masih aktif
        $exists = Application::where('student_id', $student->id)
            ->where('vacancy_id', $vacancy->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah mengajukan lamaran ke lowongan ini.');
        }

        // Mahasiswa hanya diperbolehkan memiliki satu lamaran aktif (Kaprodi Approved / Pending)
        $activeApplications = Application::where('student_id', $student->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])
            ->count();

        $maxApplications = \App\Models\Setting::getValue('max_active_applications', 3);

        if ($activeApplications >= $maxApplications) {
            return back()->with('error', "Anda memiliki terlalu banyak lamaran aktif (Maksimal {$maxApplications} lamaran aktif).");
        }

        $maxCvSize = \App\Models\Setting::getValue('max_cv_size_kb', 2048);

        $request->validate([
            'cv_file' => "required|file|mimes:pdf|max:{$maxCvSize}",
            'motivation_letter' => "nullable|file|mimes:pdf|max:{$maxCvSize}",
            'cover_letter' => 'nullable|string|max:1000',
        ]);

        $cvPath = $request->file('cv_file')->store('student_cvs', 'public');
        $mlPath = $request->hasFile('motivation_letter') ? $request->file('motivation_letter')->store('student_mls', 'public') : null;

        $application = Application::create([
            'student_id' => $student->id,
            'vacancy_id' => $vacancy->id,
            'academic_period_id' => $period->id,
            'status' => Application::STATUS_PENDING,
            'cv_file' => $cvPath,
            'motivation_letter' => $mlPath,
            'cover_letter' => $request->cover_letter,
        ]);

        // Notify corresponding Kaprodi
        $kaprodi = $application->student->studyProgram->head;
        if ($kaprodi) {
            $kaprodi->notify(new InternshipStatusNotification(
                'Pengajuan Magang Baru',
                "Mahasiswa {$student->user->name} mengajukan lamaran untuk posisi {$application->vacancy->position}. Mohon segera direview.",
                route('kaprodi.applications.show', $application->id)
            ));
        }

        return redirect()->route('student.applications.index')->with('success', 'Lamaran berhasil dikirim. Menunggu validasi Kaprodi.');
    }

    public function myApplications()
    {
        $student = $this->getStudent();
        $applications = $student->applications()
            ->with(['vacancy.industry'])
            ->latest()
            ->paginate(15);

        return view('student.applications.index', compact('applications'));
    }

    public function showApplication(Application $application)
    {
        $student = $this->getStudent();
        abort_unless( ((int) $application->student_id) === $student->id, 403);

        $application->load(['vacancy.industry', 'kaprodiReviewer']);
        return view('student.applications.show', compact('application'));
    }
}
