<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\Internship;
use Illuminate\Http\Request;

class LogbookController extends Controller
{
    private function getActiveInternship()
    {
        $student = auth()->user()->student;
        return $student ? $student->internships()->where('status', Internship::STATUS_ACTIVE)->first() : null;
    }

    public function index(Request $request)
    {
        $student = auth()->user()->student;
        if (!$student) {
            return view('student.logbooks.index', ['blocked' => true, 'reason' => 'Data mahasiswa tidak ditemukan.']);
        }

        // Cari internship aktif, atau jika tidak ada, ambil internship terakhir untuk melihat riwayat
        $internship = $student->internships()->where('status', Internship::STATUS_ACTIVE)->first()
                   ?? $student->internships()->latest('id')->first();

        if (!$internship) {
            return view('student.logbooks.index', ['blocked' => true, 'reason' => 'Anda belum memiliki riwayat program magang.']);
        }

        $query = $internship->logbooks()->with(['reviews.reviewer']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $logbooks = $query->latest('date')->paginate(15)->withQueryString();

        return view('student.logbooks.index', compact('internship', 'logbooks'));
    }

    public function export(Request $request)
    {
        $student = auth()->user()->student;
        $internship = $student->internships()->where('status', Internship::STATUS_ACTIVE)->first()
                   ?? $student->internships()->latest('id')->first();

        if (!$internship) {
            return back()->with('error', 'Anda belum memiliki riwayat program magang.');
        }

        $query = $internship->logbooks()->with(['reviews.reviewer'])->whereIn('status', ['reviewed_dpl', 'reviewed_industry']);

        if ($request->month) {
            $query->whereMonth('date', date('m', strtotime($request->month)))
                  ->whereYear('date', date('Y', strtotime($request->month)));
        }

        $logbooks = $query->orderBy('date', 'asc')->get();

        $safeName = str_replace(['/', '\\'], '-', $student->user->name);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.logbooks.export', compact('internship', 'logbooks'));
        return $pdf->download('Logbook_Magang_' . $safeName . '.pdf');
    }

    public function show(Logbook $logbook)
    {
        $student = auth()->user()->student;
        abort_unless($logbook->student_id == $student?->id, 403);

        $logbook->load(['reviews.reviewer', 'internship.vacancy.industry']);

        return view('student.logbooks.show', compact('logbook'));
    }

    public function create()
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return redirect()->route('student.logbooks.index')->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        return view('student.logbooks.create', compact('internship'));
    }

    public function store(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $maxLogbookSize = \App\Models\Setting::getValue('max_logbook_size_kb', 5120);

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'learning_outcomes' => 'nullable|string',
            'attachment' => "nullable|file|mimes:pdf,zip,rar,doc,docx,jpg,jpeg,png|max:{$maxLogbookSize}",
        ]);

        // Cek jika logbook di tanggal tersebut sudah diisi
        $exists = Logbook::where('internship_id', $internship->id)
            ->where('date', $request->date)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Anda sudah membuat logbook untuk tanggal tersebut.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('logbook_attachments', 'public');
        }

        Logbook::create([
            'internship_id' => $internship->id,
            'student_id' => $internship->student_id,
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'learning_outcomes' => $request->learning_outcomes,
            'attachment' => $attachmentPath,
            'status' => 'submitted',
        ]);

        return redirect()->route('student.logbooks.index')->with('success', 'Logbook berhasil dikirim.');
    }


    public function edit(Logbook $logbook)
    {
        $internship = $this->getActiveInternship();
        abort_unless($logbook->internship_id == $internship?->id, 403);

        if ($logbook->status !== 'revision_required') {
            return back()->with('error', 'Logbook ini tidak memerlukan revisi.');
        }

        return view('student.logbooks.edit', compact('internship', 'logbook'));
    }

    public function update(Request $request, Logbook $logbook)
    {
        $internship = $this->getActiveInternship();
        abort_unless($logbook->internship_id == $internship?->id, 403);

        if ($logbook->status !== 'revision_required') {
            return back()->with('error', 'Logbook ini tidak memerlukan revisi.');
        }

        $maxLogbookSize = \App\Models\Setting::getValue('max_logbook_size_kb', 5120);

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:20',
            'learning_outcomes' => 'nullable|string',
            'attachment' => "nullable|file|mimes:pdf,zip,rar,doc,docx,jpg,jpeg,png|max:{$maxLogbookSize}",
        ]);

        // Cek jika logbook di tanggal tersebut sudah ada (selain yang sedang direvisi)
        if ($request->date !== $logbook->date->toDateString()) {
            $exists = Logbook::where('internship_id', $internship->id)
                ->where('date', $request->date)
                ->where('id', '!=', $logbook->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Anda sudah membuat logbook untuk tanggal tersebut.');
            }
        }

        $attachmentPath = $logbook->attachment;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('logbook_attachments', 'public');
        }

        $logbook->update([
            'date' => $request->date,
            'title' => $request->title,
            'description' => $request->description,
            'learning_outcomes' => $request->learning_outcomes,
            'attachment' => $attachmentPath,
            'status' => 'submitted', // Kembali ke status submitted setelah direvisi
        ]);

        return redirect()->route('student.logbooks.index')->with('success', 'Logbook berhasil direvisi dan dikirim ulang.');
    }
}
