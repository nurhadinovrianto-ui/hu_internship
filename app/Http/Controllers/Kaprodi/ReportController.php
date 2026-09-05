<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\FinalReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $prodi = auth()->user()->managedStudyProgram();
        $query = FinalReport::with(['student.user', 'internship.vacancy.industry', 'reviewer'])
            ->where('report_type', 'dpl')
            ->whereIn('status', ['dpl_approved', 'kaprodi_received']);

        if ($prodi) {
            $query->whereHas('student', fn($q) => $q->where('study_program_id', $prodi->id));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('nim', 'like', "%{$search}%")
                         ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                  })->orWhereHas('internship.vacancy.industry', function ($iq) use ($search) {
                      $iq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest('dpl_approved_at')->paginate(15)->withQueryString();

        return view('kaprodi.reports.index', compact('reports'));
    }

    public function receive(Request $request, FinalReport $report)
    {
        $prodi = auth()->user()->managedStudyProgram();
        if ($prodi) {
            abort_unless($report->student->study_program_id == $prodi->id, 403, 'Akses ditolak.');
        }

        abort_unless($report->status === 'dpl_approved', 403, 'Laporan belum disetujui DPL.');

        $report->update([
            'status' => 'kaprodi_received',
            'kaprodi_submitted_at' => now(),
        ]);

        return redirect()->route('kaprodi.reports.index')->with('success', 'Laporan akhir berhasil diterima.');
    }
}
