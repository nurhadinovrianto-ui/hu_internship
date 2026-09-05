<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookReview;
use App\Models\Vacancy;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class LogbookController extends Controller
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

        $query = Logbook::with(['student.user', 'internship.vacancy', 'industryReview'])
            ->whereHas('internship.vacancy', function ($q) use ($supervisor) {
                $q->where('industry_supervisor_id', $supervisor->id);
            });

        if ($request->search) {
            $query->whereHas('student.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->month) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->history_tab === 'pending') {
            $query->whereDoesntHave('industryReview');
        } elseif ($request->history_tab === 'reviewed') {
            $query->whereHas('industryReview');
        }

        $logbooks = $query->latest('date')->paginate(15)->withQueryString();

        return view('industry.logbooks.index', compact('logbooks'));
    }

    public function show(Logbook $logbook)
    {
        $supervisor = $this->getSupervisor();
        abort_unless(((int) $logbook->internship->vacancy->industry_supervisor_id) === ((int) $supervisor->id), 403);

        $logbook->load(['student.user', 'reviews.reviewer']);

        return view('industry.logbooks.show', compact('logbook'));
    }

    public function review(Request $request, Logbook $logbook)
    {
        $supervisor = $this->getSupervisor();
        abort_unless(((int) $logbook->internship->vacancy->industry_supervisor_id) === ((int) $supervisor->id), 403);

        $request->validate([
            'comment' => 'required|string|max:500',
            'status' => 'required|in:noted,revision,approved',
        ]);

        LogbookReview::updateOrCreate(
            ['logbook_id' => $logbook->id, 'reviewer_type' => 'industry'],
            [
                'reviewer_id' => auth()->id(),
                'comment' => $request->comment,
                'status' => $request->status,
            ]
        );

        $newStatus = ($request->status === 'revision') ? 'revision_required' : 'reviewed_industry';
        $logbook->update(['status' => $newStatus]);

        if ($newStatus === 'revision_required') {
            $logbook->student->user->notify(new InternshipStatusNotification(
                'Revisi Logbook Diminta',
                'Supervisor Industri meminta Anda merevisi logbook tanggal ' . $logbook->date->format('d/m/Y') . '. Silakan cek komentar.',
                route('student.logbooks.show', $logbook->id)
            ));
        }

        return back()->with('success', 'Review logbook berhasil disimpan.');
    }
}
