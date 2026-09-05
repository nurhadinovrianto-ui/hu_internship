<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();
        $query = Vacancy::with(['applications'])
            ->where('industry_supervisor_id', $supervisor?->id)
            ->withCount('applications');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('division', 'like', "%{$search}%");
            });
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_published', true)
                      ->where('is_closed', false)
                      ->where('apply_deadline', '>=', now()->toDateString());
            } elseif ($request->status === 'closed') {
                $query->where(function($q) {
                    $q->where('is_closed', true)
                      ->orWhere('apply_deadline', '<', now()->toDateString());
                });
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $vacancies = $query->latest()->paginate(15)->withQueryString();

        return view('industry.vacancies.index', compact('vacancies'));
    }

    public function create()
    {
        $period = AcademicPeriod::getActive();
        return view('industry.vacancies.create', compact('period'));
    }

    public function store(Request $request)
    {
        $supervisor = $this->getSupervisor();
        
        $minDays = (int) \App\Models\Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = \Carbon\Carbon::today()->addDays($minDays)->format('Y-m-d');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'quota' => 'required|integer|min:1',
            'duration' => 'required|string|max:255',
            'apply_deadline' => 'required|date|after_or_equal:' . $minDate,
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'nullable|string|max:255',
        ]);

        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->withInput()->with('error', 'Tidak ada periode akademik aktif. Harap hubungi administrator.');
        }

        Vacancy::create(array_merge($validated, [
            'industry_id' => $supervisor->industry_id,
            'industry_supervisor_id' => $supervisor->id,
            'academic_period_id' => $period->id,
            'is_published' => true,
        ]));

        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    private function checkOwnership(Vacancy $vacancy)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            abort(403, 'Akses ditolak.');
        }
        abort_unless($vacancy->industry_id == $supervisor->industry_id || $vacancy->industry_supervisor_id == $supervisor->id, 403, 'Akses ditolak.');
    }

    public function show(Vacancy $vacancy)
    {
        $this->checkOwnership($vacancy);
        $vacancy->load(['applications.student.user', 'applications.student.studyProgram']);
        return view('industry.vacancies.show', compact('vacancy'));
    }

    public function edit(Vacancy $vacancy)
    {
        $this->checkOwnership($vacancy);
        return view('industry.vacancies.edit', compact('vacancy'));
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $this->checkOwnership($vacancy);
        $minDays = (int) \App\Models\Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = \Carbon\Carbon::today()->addDays($minDays)->format('Y-m-d');
        
        $deadlineRule = ($request->apply_deadline == $vacancy->apply_deadline?->format('Y-m-d'))
            ? 'required|date'
            : 'required|date|after_or_equal:' . $minDate;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'quota' => 'required|integer|min:1',
            'duration' => 'required|string|max:255',
            'apply_deadline' => $deadlineRule,
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'nullable|string|max:255',
            'is_closed' => 'nullable|boolean',
        ]);

        if ($request->has('is_closed')) {
            $validated['is_closed'] = (bool) $request->is_closed;
        }

        $vacancy->update($validated);
        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan berhasil diupdate.');
    }

    public function destroy(Vacancy $vacancy)
    {
        $this->checkOwnership($vacancy);
        if ($vacancy->applications()->count() > 0) {
            return back()->with('error', 'Lowongan tidak dapat dihapus karena sudah ada mahasiswa yang melamar. Silakan gunakan fitur Tutup Lowongan.');
        }

        $vacancy->delete();
        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan dihapus.');
    }

    public function toggleStatus(Vacancy $vacancy)
    {
        $this->checkOwnership($vacancy);
        $vacancy->update(['is_closed' => !$vacancy->is_closed]);
        $msg = $vacancy->is_closed ? 'ditutup' : 'dibuka kembali';
        return back()->with('success', "Lowongan berhasil {$msg}.");
    }
}
