<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipAgreement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AgreementController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();

        $query = Internship::with(['student.user', 'student.studyProgram', 'vacancy.industry', 'agreement', 'academicPeriod'])
            ->whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor?->id));

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nim', 'like', "%{$search}%")
                   ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'has_agreement') {
                $query->whereHas('agreement');
            } elseif ($request->status === 'no_agreement') {
                $query->whereDoesntHave('agreement');
            }
        }

        $internships = $query->latest()->paginate(15)->withQueryString();

        return view('industry.agreements.index', compact('internships'));
    }

    public function store(Request $request, Internship $internship)
    {
        $supervisor = $this->getSupervisor();

        // Pastikan magang milik supervisor industri ini
        if ($internship->vacancy->industry_supervisor_id !== $supervisor?->id) {
            return back()->with('error', 'Akses tidak sah untuk data magang ini.');
        }

        $validated = $request->validate([
            'agreement_number' => 'nullable|string|max:100',
            'title'            => 'required|string|max:255',
            'start_date'       => 'nullable|date',
            'end_date'         => 'nullable|date|after_or_equal:start_date',
            'allowance'        => 'nullable|string|max:150',
            'status'           => 'required|in:draft,active,completed,terminated',
            'notes'            => 'nullable|string',
            'document_file'    => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $agreement = $internship->agreement;

        if ($request->hasFile('document_file')) {
            if ($agreement && $agreement->document_file) {
                Storage::disk('public')->delete($agreement->document_file);
            }
            $validated['document_file'] = $request->file('document_file')->store('agreements', 'public');
        }

        $validated['internship_id'] = $internship->id;
        $validated['created_by']    = auth()->id();

        InternshipAgreement::updateOrCreate(
            ['internship_id' => $internship->id],
            $validated
        );

        return back()->with('success', 'Internship Agreement berhasil disimpan.');
    }

    public function destroy(InternshipAgreement $agreement)
    {
        $supervisor = $this->getSupervisor();

        if ($agreement->internship->vacancy->industry_supervisor_id !== $supervisor?->id) {
            return back()->with('error', 'Akses tidak sah.');
        }

        if ($agreement->document_file) {
            Storage::disk('public')->delete($agreement->document_file);
        }

        $agreement->delete();

        return back()->with('success', 'Internship Agreement berhasil dihapus.');
    }

    public function template(Internship $internship)
    {
        $supervisor = $this->getSupervisor();

        if ($internship->vacancy->industry_supervisor_id !== $supervisor?->id) {
            abort(403, 'Akses ditolak.');
        }

        $internship->load(['student.user', 'student.studyProgram.faculty', 'vacancy.industry', 'agreement']);

        return view('industry.agreements.template', compact('internship'));
    }
}
