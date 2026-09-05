<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\FinalReport;
use App\Models\Internship;
use Illuminate\Http\Request;
use App\Notifications\InternshipStatusNotification;

class ReportController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            return redirect()->route('dashboard.redirect')->with('error', 'Akses ditolak.');
        }

        $query = FinalReport::with(['student.user', 'internship.vacancy', 'revisions.reviewer'])
            ->where('report_type', 'industry')
            ->whereHas('internship.vacancy', function ($q) use ($supervisor) {
                $q->where('industry_supervisor_id', $supervisor->id);
            });

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('nim', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(15)->withQueryString();

        return view('industry.reports.index', compact('reports'));
    }

    public function show(FinalReport $report)
    {
        $supervisor = $this->getSupervisor();
        abort_unless($supervisor && ($report->internship?->vacancy?->industry_supervisor_id == $supervisor->id || $report->internship?->vacancy?->industry_id == $supervisor->industry_id), 403);

        $report->load(['internship.student.user', 'revisions.reviewer']);

        return view('industry.reports.show', compact('report'));
    }

    public function approve(Request $request, FinalReport $report)
    {
        $supervisor = $this->getSupervisor();
        abort_unless($supervisor && ($report->internship?->vacancy?->industry_supervisor_id == $supervisor->id || $report->internship?->vacancy?->industry_id == $supervisor->industry_id), 403);

        $request->validate([
            'status' => 'required|in:industry_approved,revision',
            'feedback' => 'nullable|string'
        ]);

        $report->update([
            'status' => $request->status,
            'dpl_feedback' => $request->feedback,
            'industry_approved_at' => $request->status === 'industry_approved' ? now() : null,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        $latestRevision = $report->revisions()->first();
        if ($latestRevision) {
            $latestRevision->update([
                'status' => $request->status,
                'feedback' => $request->feedback,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
        }

        if ($request->status === 'revision') {
            $report->internship->student->user->notify(new InternshipStatusNotification(
                'Revisi Laporan Akhir Diminta',
                'Supervisor Industri meminta Anda merevisi laporan akhir.',
                route('student.report.index')
            ));
        }

        return redirect()->route('industry.reports.index')->with('success', 'Laporan akhir berhasil diproses.');
    }
}
