<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\AcademicPeriod;
use App\Models\DplAssignment;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    public function index(Request $request)
    {
        $lecturer = $this->getLecturer();
        $period = AcademicPeriod::getActive();

        $query = Vacancy::with(['industry', 'studyProgram'])
            ->where('is_published', true)
            ->where('is_closed', false)
            ->where('apply_deadline', '>=', now()->toDateString())
            ->when($period, fn($q) => $q->where('academic_period_id', $period->id))
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('industry', fn($ind) => $ind->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        $vacancies = $query->paginate(12)->withQueryString();

        // Ambil mahasiswa bimbingan DPL yang belum ditempatkan
        $unplacedMentees = DplAssignment::with(['student.user'])
            ->where('lecturer_id', $lecturer?->id)
            ->whereNull('internship_id')
            ->when($period, fn($q) => $q->where('academic_period_id', $period->id))
            ->get();

        return view('dpl.vacancies.index', compact('vacancies', 'period', 'unplacedMentees'));
    }

    public function show(Vacancy $vacancy)
    {
        $vacancy->load(['industry', 'supervisor.user', 'studyProgram']);
        $lecturer = $this->getLecturer();
        $period = AcademicPeriod::getActive();

        $unplacedMentees = DplAssignment::with(['student.user'])
            ->where('lecturer_id', $lecturer?->id)
            ->whereNull('internship_id')
            ->when($period, fn($q) => $q->where('academic_period_id', $period->id))
            ->get();

        return view('dpl.vacancies.show', compact('vacancy', 'unplacedMentees'));
    }
}
