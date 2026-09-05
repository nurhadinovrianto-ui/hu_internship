<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\SelfProposedInternship;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class SelfProposalController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    private function authorizeProposal(SelfProposedInternship $proposal)
    {
        $lecturer = $this->getLecturer();
        abort_unless($lecturer, 403, 'Akses ditolak. Profil DPL tidak ditemukan.');

        $isAssigned = ($proposal->dpl_id == $lecturer->id) ||
            $proposal->student->dplAssignments()->where('lecturer_id', $lecturer->id)->exists();

        abort_unless($isAssigned, 403, 'Akses ditolak. Anda bukan DPL pembimbing untuk mahasiswa ini.');

        return $lecturer;
    }

    public function index(Request $request)
    {
        $lecturer = $this->getLecturer();
        if (!$lecturer) {
            abort(403, 'Profil DPL tidak ditemukan.');
        }

        $baseQuery = SelfProposedInternship::with(['student.user', 'academicPeriod'])
            ->where(function ($q) use ($lecturer) {
                $q->where('dpl_id', $lecturer->id)
                  ->orWhereHas('student.dplAssignments', function ($sq) use ($lecturer) {
                      $sq->where('lecturer_id', $lecturer->id);
                  });
            });

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
            $status = $request->status;
            if (in_array($status, ['pending', 'approved', 'revision', 'rejected'])) {
                $query->where('dpl_status', $status);
            }
        }

        $proposals = $query->latest()->paginate(15)->withQueryString();

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('dpl_status', 'pending')->count(),
            'approved' => (clone $baseQuery)->where('dpl_status', 'approved')->count(),
            'revision' => (clone $baseQuery)->where('dpl_status', 'revision')->count(),
            'rejected' => (clone $baseQuery)->where('dpl_status', 'rejected')->count(),
        ];

        return view('dpl.self-proposals.index', compact('proposals', 'stats', 'lecturer'));
    }

    public function show(SelfProposedInternship $proposal)
    {
        $lecturer = $this->authorizeProposal($proposal);

        return view('dpl.self-proposals.show', compact('proposal', 'lecturer'));
    }

    public function approve(Request $request, SelfProposedInternship $proposal)
    {
        $lecturer = $this->authorizeProposal($proposal);

        if ($proposal->dpl_status === 'approved') {
            return back()->with('error', 'Usulan magang ini telah disetujui sebelumnya oleh Anda.');
        }

        $notes = $request->input('notes', 'Usulan magang mandiri telah ditinjau dan disetujui oleh DPL. Relevansi bidang dan kompetensi sesuai.');

        $proposal->update([
            'dpl_id' => $lecturer->id,
            'dpl_status' => 'approved',
            'dpl_notes' => $notes,
            'dpl_reviewed_at' => now(),
            'status' => 'dpl_approved',
        ]);

        // Notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Usulan Magang Mandiri Disetujui DPL',
            "Dosen DPL ({$lecturer->user->name}) telah menyetujui usulan magang mandiri Anda di {$proposal->company_name}. Usulan diteruskan ke Kaprodi untuk persetujuan final.",
            route('student.self-proposals.show', $proposal->id)
        ));

        // Notifikasi ke Kaprodi
        $kaprodi = $proposal->student->studyProgram?->head;
        if ($kaprodi) {
            $kaprodi->notify(new InternshipStatusNotification(
                'Usulan Magang Mandiri Disetujui DPL - Menunggu Finalisasi',
                "Dosen DPL ({$lecturer->user->name}) telah menyetujui usulan magang mandiri mahasiswa {$proposal->student->user->name} di {$proposal->company_name}. Menunggu persetujuan final dari Kaprodi.",
                route('kaprodi.self-proposals.show', $proposal->id)
            ));
        }

        return redirect()->route('dpl.self-proposals.index')
            ->with('success', 'Usulan magang mandiri mahasiswa ' . $proposal->student->user->name . ' berhasil disetujui dan diteruskan ke Kaprodi.');
    }

    public function revision(Request $request, SelfProposedInternship $proposal)
    {
        $lecturer = $this->authorizeProposal($proposal);

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'dpl_id' => $lecturer->id,
            'dpl_status' => 'revision',
            'dpl_notes' => $request->notes,
            'dpl_reviewed_at' => now(),
            'status' => 'revision',
        ]);

        // Notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Revisi Usulan Magang Mandiri dari DPL',
            "Dosen DPL ({$lecturer->user->name}) meminta revisi untuk usulan magang mandiri di {$proposal->company_name}: {$request->notes}",
            route('student.self-proposals.edit', $proposal->id)
        ));

        return redirect()->route('dpl.self-proposals.index')
            ->with('success', 'Catatan revisi telah dikirimkan kepada mahasiswa ' . $proposal->student->user->name . '.');
    }

    public function reject(Request $request, SelfProposedInternship $proposal)
    {
        $lecturer = $this->authorizeProposal($proposal);

        $request->validate([
            'notes' => 'required|string|max:1000',
        ]);

        $proposal->update([
            'dpl_id' => $lecturer->id,
            'dpl_status' => 'rejected',
            'dpl_notes' => $request->notes,
            'dpl_reviewed_at' => now(),
            'status' => 'rejected',
        ]);

        // Notifikasi ke Mahasiswa
        $proposal->student->user->notify(new InternshipStatusNotification(
            'Usulan Magang Mandiri Ditolak oleh DPL',
            "Usulan magang mandiri Anda di {$proposal->company_name} tidak disetujui oleh Dosen DPL. Alasan: {$request->notes}",
            route('student.self-proposals.show', $proposal->id)
        ));

        return redirect()->route('dpl.self-proposals.index')
            ->with('success', 'Usulan magang mandiri telah ditolak.');
    }
}
