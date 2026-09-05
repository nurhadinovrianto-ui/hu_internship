<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\LogbookReview;
use App\Models\DplAssignment;
use App\Notifications\InternshipStatusNotification;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    private function getInternshipIds()
    {
        $lecturer = auth()->user()->lecturer;
        return DplAssignment::where('lecturer_id', $lecturer?->id)->pluck('internship_id');
    }

    public function index(Request $request)
    {
        $internshipIds = $this->getInternshipIds();
        $query = Logbook::with(['student.user', 'internship.vacancy', 'dplReview'])
            ->whereIn('internship_id', $internshipIds);

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
            $query->whereDoesntHave('dplReview');
        } elseif ($request->history_tab === 'reviewed') {
            $query->whereHas('dplReview');
        }

        $logbooks = $query->latest('date')->paginate(20)->withQueryString();
        return view('dpl.logbooks.index', compact('logbooks'));
    }

    public function show(Logbook $logbook)
    {
        $internshipIds = $this->getInternshipIds();
        abort_unless($internshipIds->contains($logbook->internship_id), 403);

        $logbook->load(['student.user', 'internship.vacancy.industry', 'reviews.reviewer']);
        return view('dpl.logbooks.show', compact('logbook'));
    }

    public function review(Request $request, Logbook $logbook)
    {
        $request->validate([
            'comment' => 'required|string|min:10',
            'status' => 'required|in:noted,revision,approved',
        ]);

        LogbookReview::updateOrCreate(
            ['logbook_id' => $logbook->id, 'reviewer_type' => 'dpl'],
            [
                'reviewer_id' => auth()->id(),
                'comment' => $request->comment,
                'status' => $request->status,
            ]
        );

        $newStatus = ($request->status === 'revision') ? 'revision_required' : 'reviewed_dpl';
        $logbook->update(['status' => $newStatus]);

        if ($newStatus === 'revision_required') {
            $logbook->student->user->notify(new InternshipStatusNotification(
                'Revisi Logbook Diminta',
                'DPL meminta Anda merevisi logbook tanggal ' . $logbook->date->format('d/m/Y') . '. Silakan cek komentar.',
                route('student.logbooks.show', $logbook->id)
            ));
        }

        return back()->with('success', 'Review logbook berhasil disimpan.');
    }
}
