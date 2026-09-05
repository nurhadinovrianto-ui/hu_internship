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

    public function index()
    {
        $supervisor = $this->getSupervisor();
        $vacancies = Vacancy::with(['applications'])
            ->where('industry_supervisor_id', $supervisor?->id)
            ->withCount('applications')
            ->latest()
            ->paginate(15);

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
            'duration_months' => 'required|integer|min:1|max:12',
            'apply_deadline' => 'required|date|after_or_equal:' . $minDate,
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'nullable|string|max:255',
        ]);

        $period = AcademicPeriod::getActive();
        Vacancy::create(array_merge($validated, [
            'industry_id' => $supervisor->industry_id,
            'industry_supervisor_id' => $supervisor->id,
            'academic_period_id' => $period?->id,
            'is_published' => true,
        ]));

        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    public function show(Vacancy $vacancy)
    {
        $vacancy->load(['applications.student.user', 'applications.student.studyProgram']);
        return view('industry.vacancies.show', compact('vacancy'));
    }

    public function edit(Vacancy $vacancy)
    {
        return view('industry.vacancies.edit', compact('vacancy'));
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $minDays = (int) \App\Models\Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = \Carbon\Carbon::today()->addDays($minDays)->format('Y-m-d');
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'quota' => 'required|integer|min:1',
            'apply_deadline' => 'required|date|after_or_equal:' . $minDate,
            'work_type' => 'required|in:onsite,remote,hybrid',
        ]);
        $vacancy->update($validated);
        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan berhasil diupdate.');
    }

    public function destroy(Vacancy $vacancy)
    {
        if ($vacancy->applications()->count() > 0) {
            return back()->with('error', 'Lowongan tidak dapat dihapus karena sudah ada mahasiswa yang melamar. Silakan gunakan fitur Tutup Lowongan.');
        }

        $vacancy->delete();
        return redirect()->route('industry.vacancies.index')->with('success', 'Lowongan dihapus.');
    }

    public function toggleStatus(Vacancy $vacancy)
    {
        $vacancy->update(['is_closed' => !$vacancy->is_closed]);
        $msg = $vacancy->is_closed ? 'ditutup' : 'dibuka kembali';
        return back()->with('success', "Lowongan berhasil {$msg}.");
    }
}
