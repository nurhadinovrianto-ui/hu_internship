<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Internship;
use App\Models\SelfProposedInternship;
use App\Models\Vacancy;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SelfProposalController extends Controller
{
    private function getProdi()
    {
        return auth()->user()->managedStudyProgram();
    }

    public function index(Request $request)
    {
        $prodi = $this->getProdi();
        $baseQuery = SelfProposedInternship::with(['student.user', 'academicPeriod', 'dpl.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));

        $query = clone $baseQuery;

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('position_title', 'like', "%{$search}%")
                  ->orWhereHas('student.user', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn($sq) => $sq->where('nim', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dpl_status')) {
            $query->where('dpl_status', $request->dpl_status);
        }

        $proposals = $query->latest()->paginate(15)->withQueryString();

        // Stats summary cards
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'submitted' => (clone $baseQuery)->where('status', 'submitted')->count(),
            'dpl_approved' => (clone $baseQuery)->where('status', 'dpl_approved')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'revision' => (clone $baseQuery)->where('status', 'revision')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return view('kaprodi.self-proposals.index', compact('proposals', 'prodi', 'stats'));
    }

    public function show(SelfProposedInternship $proposal)
    {
        $prodi = $this->getProdi();
        abort_unless($proposal->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        return view('kaprodi.self-proposals.show', compact('proposal'));
    }

    public function approve(Request $request, SelfProposedInternship $proposal)
    {
        $prodi = $this->getProdi();
        abort_unless($proposal->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        if ($proposal->status === 'approved') {
            return back()->with('error', 'Usulan magang ini sudah disetujui sebelumnya.');
        }

        // 1. Cari atau buat record Industri
        $industry = Industry::where('name', $proposal->company_name)->first();
        if (!$industry) {
            $baseSlug = Str::slug($proposal->company_name);
            $slug = $baseSlug ?: 'mitra-mandiri-' . time();
            $c = 1;
            while (Industry::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$c}";
                $c++;
            }

            $industry = Industry::create([
                'name' => $proposal->company_name,
                'slug' => $slug,
                'industry_type' => $proposal->industry_sector ?? 'Teknologi Informasi & Komunikasi',
                'address' => $proposal->company_address,
                'city' => 'Jakarta',
                'email' => $proposal->contact_person_email ?? ('info@' . Str::slug($proposal->company_name) . '.com'),
                'phone' => $proposal->contact_person_phone ?? '021-000000',
                'contact_person' => $proposal->contact_person_name,
                'latitude' => $proposal->latitude,
                'longitude' => $proposal->longitude,
                'partnership_status' => 'none',
                'is_active' => true,
            ]);
        }

        // 2. Cari atau buat Supervisor Industri & Akun User
        $supEmail = $proposal->contact_person_email ?: ('pic.' . $industry->slug . '@simang.ac.id');
        $supUser = \App\Models\User::where('email', $supEmail)->first();
        $plainPassword = null;

        if (!$supUser) {
            $plainPassword = 'Mitra@' . date('Y') . '#' . rand(1000, 9999);
            $supUser = \App\Models\User::create([
                'name' => $proposal->contact_person_name,
                'email' => $supEmail,
                'password' => bcrypt($plainPassword),
                'phone' => $proposal->contact_person_phone,
                'status' => 'active',
            ]);
        }

        if (!$supUser->hasRole('supervisor-industri')) {
            $supUser->assignRole('supervisor-industri');
        }

        $supervisor = \App\Models\IndustrySupervisor::where('user_id', $supUser->id)->first();
        if (!$supervisor) {
            $supervisor = \App\Models\IndustrySupervisor::create([
                'user_id' => $supUser->id,
                'industry_id' => $industry->id,
                'position' => $proposal->contact_person_position ?? 'Mentor Magang Mandiri',
                'is_primary' => true,
            ]);
        }

        // 3. Buat lowongan mandiri khusus
        $periodId = $proposal->academic_period_id ?? \App\Models\AcademicPeriod::getActive()?->id ?? 1;
        $vacancy = Vacancy::create([
            'industry_id' => $industry->id,
            'industry_supervisor_id' => $supervisor->id,
            'academic_period_id' => $periodId,
            'title' => $proposal->position_title . ' (Magang Mandiri)',
            'position' => $proposal->position_title,
            'description' => $proposal->job_description,
            'requirements' => 'Penerimaan Mandiri oleh Perusahaan',
            'quota' => 1,
            'duration' => 6,
            'apply_deadline' => $proposal->start_date,
            'is_published' => false,
            'is_closed' => true, // agar tidak dilamar mahasiswa lain
        ]);

        // 4. Buat record Application resmi
        $application = \App\Models\Application::create([
            'student_id' => $proposal->student_id,
            'vacancy_id' => $vacancy->id,
            'academic_period_id' => $periodId,
            'status' => 'industry_accepted',
            'kaprodi_notes' => 'Magang Mandiri Disetujui Kaprodi',
            'kaprodi_reviewed_at' => now(),
            'kaprodi_reviewed_by' => auth()->id(),
            'cv_file' => $proposal->loa_file_path,
        ]);

        // 5. Cek plotting DPL atau hubungkan DPL usulan
        $preAssignment = \App\Models\DplAssignment::where('student_id', $proposal->student_id)
            ->where('academic_period_id', $periodId)
            ->first();

        if (!$preAssignment && $proposal->dpl_id) {
            $preAssignment = \App\Models\DplAssignment::create([
                'student_id' => $proposal->student_id,
                'lecturer_id' => $proposal->dpl_id,
                'academic_period_id' => $periodId,
                'status' => 'assigned',
            ]);
        }

        $internshipStatus = $preAssignment ? Internship::STATUS_ACTIVE : Internship::STATUS_WAITING_DPL;

        $internship = Internship::create([
            'application_id' => $application->id,
            'student_id' => $proposal->student_id,
            'vacancy_id' => $vacancy->id,
            'academic_period_id' => $periodId,
            'start_date' => $proposal->start_date,
            'end_date' => $proposal->end_date,
            'status' => $internshipStatus,
        ]);

        if ($preAssignment) {
            $preAssignment->update(['internship_id' => $internship->id]);

            // Notify DPL
            $preAssignment->lecturer->user->notify(new InternshipStatusNotification(
                'Mahasiswa Bimbingan Disetujui Magang Mandiri',
                "Mahasiswa bimbingan Anda {$proposal->student->user->name} telah disetujui usulan magang mandirinya di {$proposal->company_name}. Status magang kini Aktif.",
                route('dpl.students')
            ));
        }

        // 6. Update status usulan mandiri & kredensial akun mitra
        $proposalNotes = $request->input('notes') ?? ($preAssignment
            ? "Usulan magang mandiri disetujui. DPL pembimbing ({$preAssignment->lecturer->user->name}) telah aktif terhubung dan akun mentor mitra telah dibuat."
            : "Usulan magang mandiri disetujui. Akun mitra berhasil dibuat. Silakan menunggu plotting DPL.");

        $proposal->update([
            'status' => 'approved',
            'kaprodi_notes' => $proposalNotes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'internship_id' => $internship->id,
            'partner_user_id' => $supUser->id,
            'partner_temp_password' => $plainPassword ?? ($proposal->partner_temp_password ?? '(Menggunakan akun mitra terdaftar)'),
            'partner_account_created' => true,
        ]);

        // 7. Kirim notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Usulan Magang Mandiri Disetujui!',
            "Selamat! Usulan magang mandiri Anda di {$proposal->company_name} telah disetujui resmi oleh Kaprodi. Akun mentor/supervisor mitra telah dibuat otomatis ({$supEmail}).",
            route('student.self-proposals.show', $proposal->id)
        ));

        // 8. Kirim notifikasi ke Mentor Mitra jika baru dibuat
        if ($plainPassword) {
            $supUser->notify(new InternshipStatusNotification(
                'Akun Pembimbing Industri SIMANG Telah Dibuat',
                "Halo {$proposal->contact_person_name}, akun pembimbing industri Anda untuk memantau mahasiswa {$proposal->student->user->name} di {$proposal->company_name} telah diaktifkan. Silakan login dengan Email: {$supEmail} dan Password: {$plainPassword}",
                route('login')
            ));
        }

        $successMsg = 'Usulan magang mandiri berhasil disetujui! Akun mentor mitra otomatis dibuat (' . $supEmail . ').';
        if ($preAssignment) {
            $successMsg .= ' Dosen DPL (' . $preAssignment->lecturer->user->name . ') telah otomatis terhubung dan status magang langsung Aktif.';
        } else {
            $successMsg .= ' Status magang kini masuk ke antrean plotting DPL.';
        }

        return redirect()->route('kaprodi.self-proposals.index')
            ->with('success', $successMsg);
    }

    public function reject(Request $request, SelfProposedInternship $proposal)
    {
        $prodi = $this->getProdi();
        abort_unless($proposal->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'status' => 'rejected',
            'kaprodi_notes' => $request->notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Usulan Magang Mandiri Ditolak oleh Kaprodi',
            "Usulan magang mandiri Anda di {$proposal->company_name} ditolak oleh Kaprodi. Alasan: {$request->notes}",
            route('student.self-proposals.show', $proposal->id)
        ));

        return redirect()->route('kaprodi.self-proposals.index')
            ->with('success', 'Usulan magang mandiri telah ditolak.');
    }

    public function revision(Request $request, SelfProposedInternship $proposal)
    {
        $prodi = $this->getProdi();
        abort_unless($proposal->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'status' => 'revision',
            'kaprodi_notes' => $request->notes,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        // Notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Revisi Usulan Magang Mandiri dari Kaprodi',
            "Kaprodi meminta perbaikan pada usulan magang mandiri Anda di {$proposal->company_name}: {$request->notes}",
            route('student.self-proposals.edit', $proposal->id)
        ));

        return redirect()->route('kaprodi.self-proposals.index')
            ->with('success', 'Catatan revisi telah dikirimkan ke mahasiswa.');
    }
}
