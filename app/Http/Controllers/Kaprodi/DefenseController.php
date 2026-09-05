<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\InternshipDefense;
use App\Models\Lecturer;
use Illuminate\Http\Request;

class DefenseController extends Controller
{
    private function getProdi()
    {
        return auth()->user()->managedStudyProgram();
    }

    public function index(Request $request)
    {
        $prodi = $this->getProdi();
        $query = InternshipDefense::with(['student.user', 'internship.vacancy.industry', 'examiner.user', 'supervisor.user', 'scores'])
            ->whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('student', fn($sq) => $sq->where('nim', 'like', "%{$search}%"))
                  ->orWhereHas('internship.vacancy.industry', fn($iq) => $iq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $defenses = $query->latest()->paginate(15)->withQueryString();

        // Daftar Dosen untuk penugasan penguji
        $lecturers = Lecturer::with('user')
            ->where('study_program_id', $prodi?->id)
            ->get();

        // Stats
        $stats = [
            'total' => (clone $query)->count(),
            'registered' => InternshipDefense::whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))->where('status', 'registered')->count(),
            'scheduled' => InternshipDefense::whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))->where('status', 'scheduled')->count(),
            'passed' => InternshipDefense::whereHas('student', fn($q) => $q->where('study_program_id', $prodi?->id))->where('status', 'passed')->count(),
        ];

        return view('kaprodi.defenses.index', compact('defenses', 'lecturers', 'prodi', 'stats'));
    }

    public function schedule(Request $request, InternshipDefense $defense)
    {
        $prodi = $this->getProdi();
        abort_unless($defense->student->study_program_id == $prodi?->id, 403, 'Akses ditolak.');

        $request->validate([
            'scheduled_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room_or_link' => 'required|string|max:255',
            'examiner_lecturer_id' => 'required|exists:lecturers,id',
        ]);

        $officialNumber = $defense->official_report_number ?? sprintf(
            'BA-MAGANG/%s/%s/%04d',
            date('Y'),
            date('m'),
            $defense->id
        );

        $defense->update([
            'scheduled_date' => $request->scheduled_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'room_or_link' => $request->room_or_link,
            'examiner_lecturer_id' => $request->examiner_lecturer_id,
            'status' => 'scheduled',
            'official_report_number' => $officialNumber,
        ]);

        return redirect()->route('kaprodi.defenses.index')
            ->with('success', 'Jadwal dan Dosen Penguji sidang ujian magang mahasiswa ' . $defense->student->user->name . ' berhasil ditetapkan!');
    }
}
