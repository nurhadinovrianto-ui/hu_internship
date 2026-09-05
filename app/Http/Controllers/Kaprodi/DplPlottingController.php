<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\Lecturer;
use App\Models\Student;
use App\Models\AcademicPeriod;
use App\Models\DplAssignment;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class DplPlottingController extends Controller
{
    private function getStudyProgram()
    {
        return auth()->user()->managedStudyProgram();
    }

    public function index(Request $request)
    {
        $prodi = $this->getStudyProgram();
        $period = AcademicPeriod::getActive();
        $tab = $request->get('tab', 'pre_placement');

        $search = $request->search;

        // Base query for internships
        $baseInternshipQuery = Internship::with(['student.user', 'vacancy.industry', 'dplAssignment.lecturer.user'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));

        if ($period) {
            $baseInternshipQuery->where('academic_period_id', $period->id);
        }

        if ($request->filled('search')) {
            $baseInternshipQuery->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy', function ($vq) use ($search) {
                    $vq->where('title', 'like', "%{$search}%")
                       ->orWhere('position', 'like', "%{$search}%")
                       ->orWhereHas('industry', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
                })->orWhereHas('dplAssignment.lecturer.user', function ($dq) use ($search) {
                    $dq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $internships = collect();
        $students = collect();

        if ($tab === 'pre_placement') {
            // Mahasiswa prodi periode aktif yang BELUM ditempatkan di magang
            $studentQuery = Student::with([
                'user',
                'studyProgram',
                'applications.vacancy.industry',
                'dplAssignments' => fn($q) => $q->when($period, fn($pq) => $pq->where('academic_period_id', $period->id))->with('lecturer.user'),
            ])
            ->where('study_program_id', $prodi?->id)
            ->whereDoesntHave('internships', function ($iq) use ($period) {
                $iq->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])
                   ->when($period, fn($pq) => $pq->where('academic_period_id', $period->id));
            });

            if ($request->filled('search')) {
                $studentQuery->where(function ($q) use ($search) {
                    $q->where('nim', 'like', "%{$search}%")
                      ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            }

            if ($request->filled('pre_status')) {
                if ($request->pre_status === 'assigned') {
                    $studentQuery->whereHas('dplAssignments', fn($dq) => $dq->when($period, fn($pq) => $pq->where('academic_period_id', $period->id)));
                } elseif ($request->pre_status === 'unassigned') {
                    $studentQuery->whereDoesntHave('dplAssignments', fn($dq) => $dq->when($period, fn($pq) => $pq->where('academic_period_id', $period->id)));
                }
            }

            $students = $studentQuery->latest()->paginate(15)->withQueryString();

        } elseif ($tab === 'assigned') {
            $internships = (clone $baseInternshipQuery)
                ->whereHas('dplAssignment')
                ->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_WAITING_DPL])
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else { // waiting
            $internships = (clone $baseInternshipQuery)
                ->where('status', Internship::STATUS_WAITING_DPL)
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        $lecturersQuery = Lecturer::with('user');
        $lecturers = $lecturersQuery->get()
            ->map(fn($l) => [
                'id' => $l->id,
                'name' => $l->user->name,
                'current_mentee' => $l->current_mentee_count,
                'max_mentee' => $l->max_mentee,
                'has_capacity' => $l->hasCapacity(),
            ]);

        // Statistik plotting
        $countQuery = Internship::whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))
            ->when($period, fn($pq) => $pq->where('academic_period_id', $period->id));
        $waitingCount = (clone $countQuery)->where('status', Internship::STATUS_WAITING_DPL)->count();
        $assignedCount = (clone $countQuery)->whereHas('dplAssignment')->where('status', Internship::STATUS_ACTIVE)->count();

        // Hitung mahasiswa pra-penempatan (belum ditempatkan)
        $prePlacementTotal = Student::where('study_program_id', $prodi?->id)
            ->whereDoesntHave('internships', function ($iq) use ($period) {
                $iq->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])
                   ->when($period, fn($pq) => $pq->where('academic_period_id', $period->id));
            })->count();

        $prePlacementAssigned = Student::where('study_program_id', $prodi?->id)
            ->whereDoesntHave('internships', function ($iq) use ($period) {
                $iq->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])
                   ->when($period, fn($pq) => $pq->where('academic_period_id', $period->id));
            })
            ->whereHas('dplAssignments', fn($dq) => $dq->when($period, fn($pq) => $pq->where('academic_period_id', $period->id)))
            ->count();

        $totalLecturers = $lecturers->count();
        $totalAllMentees = $assignedCount + $prePlacementAssigned;
        $avgMentee = $totalLecturers > 0 ? round($totalAllMentees / $totalLecturers, 1) : 0;

        $stats = [
            'pre_placement_total' => $prePlacementTotal,
            'pre_placement_assigned' => $prePlacementAssigned,
            'pre_placement_unassigned' => max(0, $prePlacementTotal - $prePlacementAssigned),
            'waiting' => $waitingCount,
            'assigned' => $assignedCount,
            'total_lecturers' => $totalLecturers,
            'avg_mentee' => $avgMentee,
        ];

        return view('kaprodi.dpl-plotting.index', compact('internships', 'students', 'lecturers', 'prodi', 'period', 'tab', 'stats'));
    }

    public function assignPrePlacement(Request $request, Student $student)
    {
        $prodi = $this->getStudyProgram();
        abort_unless($student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->with('error', 'Tidak ada periode akademik aktif.');
        }

        $lecturer = Lecturer::with('user')->findOrFail($request->lecturer_id);

        if (!$lecturer->hasCapacity()) {
            return back()->with('error', "Dosen {$lecturer->user->name} sudah mencapai batas maksimal bimbingan ({$lecturer->current_mentee_count}/{$lecturer->max_mentee}).");
        }

        // Cari atau buat assignment pra-penempatan
        $assignment = DplAssignment::where('student_id', $student->id)
            ->where('academic_period_id', $period->id)
            ->whereNull('internship_id')
            ->first();

        if ($assignment) {
            $assignment->update([
                'lecturer_id' => $lecturer->id,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'notes' => $request->notes ?? $assignment->notes,
            ]);
        } else {
            DplAssignment::create([
                'student_id' => $student->id,
                'academic_period_id' => $period->id,
                'internship_id' => null,
                'lecturer_id' => $lecturer->id,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'notes' => $request->notes,
            ]);
        }

        // Notifikasi ke Mahasiswa
        $student->user->notify(new InternshipStatusNotification(
            'Dosen Pembimbing Lapangan (DPL) Ditugaskan',
            "Bapak/Ibu {$lecturer->user->name} telah ditugaskan sebagai DPL Anda untuk periode {$period->name}. DPL akan mendampingi dan membantu Anda dalam persiapan serta pencarian tempat magang.",
            route('student.dashboard')
        ));

        // Notifikasi ke Dosen DPL
        $lecturer->user->notify(new InternshipStatusNotification(
            'Mahasiswa Bimbingan Baru (Pra-Penempatan)',
            "Anda ditugaskan sebagai DPL untuk mahasiswa {$student->user->name} (NIM: {$student->nim}). Anda dapat memantau dan membantu mengarahkan pencarian tempat magang mahasiswa ini.",
            route('dpl.students')
        ));

        return back()->with('success', "Dosen {$lecturer->user->name} berhasil ditugaskan sebagai DPL mahasiswa {$student->user->name}.");
    }

    public function removePrePlacement(Student $student)
    {
        $prodi = $this->getStudyProgram();
        abort_unless($student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $period = AcademicPeriod::getActive();
        $assignment = DplAssignment::where('student_id', $student->id)
            ->where('academic_period_id', $period?->id)
            ->whereNull('internship_id')
            ->first();

        if ($assignment) {
            $assignment->delete();
            return back()->with('success', "Penugasan DPL pra-penempatan untuk {$student->user->name} berhasil dibatalkan.");
        }

        return back()->with('error', 'Penugasan tidak ditemukan.');
    }

    public function assign(Request $request, Internship $internship)
    {
        $prodi = $this->getStudyProgram();
        abort_unless($internship->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate(['lecturer_id' => 'required|exists:lecturers,id']);

        $lecturer = Lecturer::findOrFail($request->lecturer_id);

        if (!$lecturer->hasCapacity()) {
            return back()->with('error', "Dosen {$lecturer->user->name} sudah mencapai batas maksimal bimbingan ({$lecturer->current_mentee_count}/{$lecturer->max_mentee}).");
        }

        // Hapus penugasan pra-penempatan lama milik mahasiswa ini agar tidak ada duplikasi
        DplAssignment::where('student_id', $internship->student_id)
            ->whereNull('internship_id')
            ->delete();

        // Hapus assignment lama jika ada
        $internship->dplAssignment?->delete();

        DplAssignment::create([
            'internship_id' => $internship->id,
            'student_id' => $internship->student_id,
            'academic_period_id' => $internship->academic_period_id,
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

    public function reassign(Request $request, Internship $internship)
    {
        $prodi = $this->getStudyProgram();
        abort_unless($internship->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'lecturer_id' => 'required|exists:lecturers,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $newLecturer = Lecturer::with('user')->findOrFail($request->lecturer_id);
        $oldAssignment = $internship->dplAssignment;
        $oldLecturer = $oldAssignment?->lecturer;

        if ($oldLecturer && $oldLecturer->id == $newLecturer->id) {
            return back()->with('error', "Dosen {$newLecturer->user->name} sudah merupakan DPL mahasiswa ini.");
        }

        if (!$newLecturer->hasCapacity()) {
            return back()->with('error', "Dosen {$newLecturer->user->name} sudah mencapai batas maksimal bimbingan ({$newLecturer->current_mentee_count}/{$newLecturer->max_mentee}).");
        }

        // Hapus penugasan pra-penempatan lama milik mahasiswa ini jika ada
        DplAssignment::where('student_id', $internship->student_id)
            ->whereNull('internship_id')
            ->delete();

        // Simpan / update assignment
        if ($oldAssignment) {
            $oldAssignment->update([
                'student_id' => $internship->student_id,
                'academic_period_id' => $internship->academic_period_id,
                'lecturer_id' => $newLecturer->id,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'notes' => $request->reason ?? $oldAssignment->notes,
            ]);
        } else {
            DplAssignment::create([
                'internship_id' => $internship->id,
                'student_id' => $internship->student_id,
                'academic_period_id' => $internship->academic_period_id,
                'lecturer_id' => $newLecturer->id,
                'assigned_by' => auth()->id(),
                'assigned_at' => now(),
                'notes' => $request->reason,
            ]);
        }

        // Jika status sebelumnya waiting_dpl, aktifkan
        if ($internship->status === Internship::STATUS_WAITING_DPL) {
            $internship->update([
                'status' => Internship::STATUS_ACTIVE,
                'start_date' => $internship->start_date ?? now()->toDateString(),
            ]);
        }

        $studentUser = $internship->student->user;
        $industryName = $internship->vacancy->industry->name ?? 'Mitra Industri';
        $reasonText = $request->reason ? " Alasan pergantian: {$request->reason}." : "";

        // 1. Notifikasi ke DPL lama
        if ($oldLecturer && $oldLecturer->user && $oldLecturer->id != $newLecturer->id) {
            $oldLecturer->user->notify(new InternshipStatusNotification(
                'Pengalihan Bimbingan DPL Mahasiswa',
                "Bimbingan untuk mahasiswa {$studentUser->name} ({$industryName}) telah dialihkan kepada {$newLecturer->user->name}.{$reasonText}",
                route('dpl.students')
            ));
        }

        // 2. Notifikasi ke DPL baru
        $newLecturer->user->notify(new InternshipStatusNotification(
            'Penugasan DPL Pengganti',
            "Anda ditugaskan sebagai DPL baru untuk mahasiswa {$studentUser->name} ({$industryName}).{$reasonText}",
            route('dpl.students')
        ));

        // 3. Notifikasi ke Mahasiswa
        $studentUser->notify(new InternshipStatusNotification(
            'Perubahan Dosen Pembimbing Lapangan (DPL)',
            "Dosen Pembimbing Lapangan (DPL) Anda untuk magang di {$industryName} telah diperbarui menjadi {$newLecturer->user->name}.{$reasonText}",
            route('student.dashboard')
        ));

        $actionWord = $oldLecturer ? 'diubah menjadi' : 'berhasil ditugaskan ke';
        return back()->with('success', "DPL untuk {$studentUser->name} {$actionWord} {$newLecturer->user->name}.");
    }
}
