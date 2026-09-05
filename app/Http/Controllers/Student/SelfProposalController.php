<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\SelfProposedInternship;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SelfProposalController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    public function index()
    {
        $student = $this->getStudent();
        $proposals = SelfProposedInternship::with(['dpl.user', 'academicPeriod'])
            ->where('student_id', $student->id)
            ->latest()
            ->paginate(10);

        $hasActiveInternship = $student->internships()->whereIn('status', ['waiting_dpl', 'active'])->exists();

        return view('student.self-proposals.index', compact('proposals', 'hasActiveInternship'));
    }

    public function create()
    {
        $student = $this->getStudent();
        
        if ($student->internships()->whereIn('status', ['waiting_dpl', 'active'])->exists()) {
            return redirect()->route('student.self-proposals.index')
                ->with('error', 'Anda sudah memiliki program magang yang sedang aktif.');
        }

        $activePeriod = AcademicPeriod::getActive();
        $assignedDpl = $student->getDplForPeriod($activePeriod?->id);

        return view('student.self-proposals.create', compact('student', 'activePeriod', 'assignedDpl'));
    }

    public function store(Request $request)
    {
        $student = $this->getStudent();

        $request->validate([
            'company_name' => 'required|string|max:255',
            'industry_sector' => 'nullable|string|max:100',
            'company_address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'nullable|integer|min:50|max:5000',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_position' => 'nullable|string|max:100',
            'contact_person_email' => 'required|email|max:255',
            'contact_person_phone' => 'nullable|string|max:30',
            'position_title' => 'required|string|max:255',
            'job_description' => 'required|string|max:2000',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'loa_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $loaPath = $request->file('loa_file')->store('loa_documents', 'public');
        $activePeriod = AcademicPeriod::getActive();
        $assignedDpl = $student->getDplForPeriod($activePeriod?->id);

        $proposal = SelfProposedInternship::create([
            'student_id' => $student->id,
            'academic_period_id' => $activePeriod?->id,
            'dpl_id' => $assignedDpl?->id,
            'dpl_status' => 'pending',
            'company_name' => $request->company_name,
            'industry_sector' => $request->industry_sector,
            'company_address' => $request->company_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'geofence_radius' => $request->geofence_radius ?? 500,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_position' => $request->contact_person_position,
            'contact_person_email' => $request->contact_person_email,
            'contact_person_phone' => $request->contact_person_phone,
            'position_title' => $request->position_title,
            'job_description' => $request->job_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'loa_file_path' => $loaPath,
            'status' => 'submitted',
        ]);

        // Kirim notifikasi ke DPL jika ada
        if ($assignedDpl && $assignedDpl->user) {
            $assignedDpl->user->notify(new InternshipStatusNotification(
                'Usulan Magang Mandiri Mahasiswa',
                "Mahasiswa bimbingan Anda {$student->user->name} mengajukan usulan tempat magang mandiri di {$proposal->company_name}. Silakan lakukan peninjauan.",
                route('dpl.self-proposals.show', $proposal->id)
            ));
        }

        // Kirim notifikasi ke Kaprodi
        $kaprodi = $student->studyProgram?->head;
        if ($kaprodi) {
            $kaprodi->notify(new InternshipStatusNotification(
                'Pengajuan Usulan Magang Mandiri Baru',
                "Mahasiswa {$student->user->name} mengajukan usulan magang mandiri di {$proposal->company_name}.",
                route('kaprodi.self-proposals.show', $proposal->id)
            ));
        }

        return redirect()->route('student.self-proposals.index')
            ->with('success', 'Usulan magang mandiri berhasil diajukan! Usulan akan ditinjau oleh Dosen DPL dan diverifikasi oleh Koordinator Program Studi.');
    }

    public function show(SelfProposedInternship $proposal)
    {
        $student = $this->getStudent();
        abort_unless($proposal->student_id == $student->id, 403, 'Akses ditolak.');

        return view('student.self-proposals.show', compact('proposal'));
    }

    public function edit(SelfProposedInternship $proposal)
    {
        $student = $this->getStudent();
        abort_unless($proposal->student_id == $student->id, 403, 'Akses ditolak.');

        if (!in_array($proposal->status, ['submitted', 'revision'])) {
            return redirect()->route('student.self-proposals.show', $proposal->id)
                ->with('error', 'Usulan ini tidak dapat diubah karena sudah diproses atau disetujui.');
        }

        $activePeriod = AcademicPeriod::getActive();
        $assignedDpl = $student->getDplForPeriod($activePeriod?->id);

        return view('student.self-proposals.edit', compact('proposal', 'student', 'activePeriod', 'assignedDpl'));
    }

    public function update(Request $request, SelfProposedInternship $proposal)
    {
        $student = $this->getStudent();
        abort_unless($proposal->student_id == $student->id, 403, 'Akses ditolak.');

        if (!in_array($proposal->status, ['submitted', 'revision'])) {
            return back()->with('error', 'Usulan ini tidak dapat diubah karena sudah diproses.');
        }

        $request->validate([
            'company_name' => 'required|string|max:255',
            'industry_sector' => 'nullable|string|max:100',
            'company_address' => 'required|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'geofence_radius' => 'nullable|integer|min:50|max:5000',
            'contact_person_name' => 'required|string|max:255',
            'contact_person_position' => 'nullable|string|max:100',
            'contact_person_email' => 'required|email|max:255',
            'contact_person_phone' => 'nullable|string|max:30',
            'position_title' => 'required|string|max:255',
            'job_description' => 'required|string|max:2000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'loa_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $data = [
            'company_name' => $request->company_name,
            'industry_sector' => $request->industry_sector,
            'company_address' => $request->company_address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'geofence_radius' => $request->geofence_radius ?? 500,
            'contact_person_name' => $request->contact_person_name,
            'contact_person_position' => $request->contact_person_position,
            'contact_person_email' => $request->contact_person_email,
            'contact_person_phone' => $request->contact_person_phone,
            'position_title' => $request->position_title,
            'job_description' => $request->job_description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'submitted',
            'dpl_status' => 'pending',
        ];

        if ($request->hasFile('loa_file')) {
            if ($proposal->loa_file_path && Storage::disk('public')->exists($proposal->loa_file_path)) {
                Storage::disk('public')->delete($proposal->loa_file_path);
            }
            $data['loa_file_path'] = $request->file('loa_file')->store('loa_documents', 'public');
        }

        $activePeriod = AcademicPeriod::getActive();
        $assignedDpl = $student->getDplForPeriod($activePeriod?->id);
        if ($assignedDpl) {
            $data['dpl_id'] = $assignedDpl->id;
        }

        $proposal->update($data);

        // Beritahu DPL jika ada
        if ($assignedDpl && $assignedDpl->user) {
            $assignedDpl->user->notify(new InternshipStatusNotification(
                'Perbaikan Usulan Magang Mandiri',
                "Mahasiswa bimbingan Anda {$student->user->name} telah memperbarui usulan magang mandiri di {$proposal->company_name}.",
                route('dpl.self-proposals.show', $proposal->id)
            ));
        }

        return redirect()->route('student.self-proposals.show', $proposal->id)
            ->with('success', 'Usulan magang mandiri berhasil diperbarui dan diajukan ulang untuk ditinjau.');
    }
}
