<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\AcademicPeriod;
use App\Models\IndustrySupervisor;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Models\Industry;

class VacancyController extends Controller
{
    public function index(Request $request)
    {
        $query = Vacancy::with(['industry', 'supervisor.user', 'applications'])
            ->withCount('applications')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('industry', function($ind) use ($search) {
                      $ind->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('industry_id')) {
            $query->where('industry_id', $request->industry_id);
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

        $vacancies = $query->paginate(15)->withQueryString();
        $industries = Industry::orderBy('name')->get();

        return view('admin.vacancies.index', compact('vacancies', 'industries'));
    }

    public function create()
    {
        $period = AcademicPeriod::getActive();
        $supervisors = IndustrySupervisor::with(['user', 'industry'])->get();
        return view('admin.vacancies.create', compact('period', 'supervisors'));
    }

    public function store(Request $request)
    {
        $minDays = (int) Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = Carbon::today()->addDays($minDays)->format('Y-m-d');
        
        $validated = $request->validate([
            'industry_supervisor_id' => 'required|exists:industry_supervisors,id',
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

        $supervisor = IndustrySupervisor::findOrFail($request->industry_supervisor_id);
        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->withInput()->with('error', 'Tidak ada periode akademik aktif. Silakan buat atau aktifkan periode akademik terlebih dahulu.');
        }
        
        Vacancy::create(array_merge($validated, [
            'industry_id' => $supervisor->industry_id,
            'academic_period_id' => $period->id,
            'is_published' => true,
        ]));

        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil dipublikasikan.');
    }

    public function show(Vacancy $vacancy)
    {
        return redirect()->route('admin.vacancies.edit', $vacancy);
    }

    public function edit(Vacancy $vacancy)
    {
        $supervisors = IndustrySupervisor::with(['user', 'industry'])->get();
        return view('admin.vacancies.edit', compact('vacancy', 'supervisors'));
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $minDays = (int) Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = Carbon::today()->addDays($minDays)->format('Y-m-d');
        
        $deadlineRule = ($request->apply_deadline == $vacancy->apply_deadline?->format('Y-m-d'))
            ? 'required|date'
            : 'required|date|after_or_equal:' . $minDate;

        $validated = $request->validate([
            'industry_supervisor_id' => 'required|exists:industry_supervisors,id',
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

        $supervisor = IndustrySupervisor::findOrFail($request->industry_supervisor_id);
        
        $updateData = array_merge($validated, [
            'industry_id' => $supervisor->industry_id,
        ]);

        if ($request->has('is_closed')) {
            $updateData['is_closed'] = (bool) $request->is_closed;
        }

        $vacancy->update($updateData);

        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan berhasil diupdate.');
    }

    public function destroy(Vacancy $vacancy)
    {
        if ($vacancy->applications()->count() > 0) {
            return back()->with('error', 'Lowongan tidak dapat dihapus karena sudah ada mahasiswa yang melamar. Silakan gunakan fitur Tutup Lowongan.');
        }

        $vacancy->delete();
        return redirect()->route('admin.vacancies.index')->with('success', 'Lowongan dihapus.');
    }

    public function toggleStatus(Vacancy $vacancy)
    {
        $vacancy->update(['is_closed' => !$vacancy->is_closed]);
        $msg = $vacancy->is_closed ? 'ditutup' : 'dibuka kembali';
        return back()->with('success', "Lowongan berhasil {$msg}.");
    }
}
