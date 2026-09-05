<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\FinalReport;
use App\Models\DplAssignment;
use Illuminate\Http\Request;
use App\Notifications\InternshipStatusNotification;

class ReportController extends Controller
{
    private function getInternshipIds()
    {
        $lecturer = auth()->user()->lecturer;
        return DplAssignment::where('lecturer_id', $lecturer?->id)->pluck('internship_id');
    }

    public function index(Request $request)
    {
        $internshipIds = $this->getInternshipIds();
        $query = FinalReport::with(['student.user', 'internship.vacancy.industry', 'revisions.reviewer'])
            ->whereIn('internship_id', $internshipIds)
            ->where('report_type', 'dpl');

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

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('dpl.reports', compact('reports'));
    }

    public function approve(Request $request, FinalReport $report)
    {
        $internshipIds = $this->getInternshipIds();
        abort_unless($internshipIds->contains($report->internship_id), 403);

        $request->validate([
            'status' => 'required|in:dpl_approved,revision',
            'dpl_feedback' => 'nullable|string'
        ]);

        $report->update([
            'status' => $request->status,
            'dpl_feedback' => $request->dpl_feedback,
            'dpl_approved_at' => $request->status === 'dpl_approved' ? now() : null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        $latestRevision = $report->revisions()->first();
        if ($latestRevision) {
            $latestRevision->update([
                'status' => $request->status,
                'feedback' => $request->dpl_feedback,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        }

        if ($request->status === 'revision') {
            $report->internship->student->user->notify(new InternshipStatusNotification(
                'Revisi Laporan Akhir Diminta',
                'DPL meminta Anda merevisi laporan akhir.',
                route('student.report.index')
            ));
        }

        return redirect()->route('dpl.reports.index')->with('success', 'Review laporan akhir berhasil disimpan.');
    }
}
