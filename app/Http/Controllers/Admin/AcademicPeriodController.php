<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicPeriod::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('year', 'like', "%{$search}%");
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $periods = $query->latest()->paginate(10)->withQueryString();
        return view('admin.periods.index', compact('periods'));
    }

    public function create() { return redirect()->route('admin.periods.index'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|string|max:10',
            'semester' => 'required|in:ganjil,genap',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'apply_start' => 'nullable|date',
            'apply_end' => 'nullable|date|after_or_equal:apply_start',
        ]);
        AcademicPeriod::create($validated);
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil ditambah.');
    }

    public function show(AcademicPeriod $period) { return redirect()->route('admin.periods.edit', $period); }

    public function edit(AcademicPeriod $period) { return view('admin.periods.edit', compact('period')); }

    public function update(Request $request, AcademicPeriod $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'apply_start' => 'nullable|date',
            'apply_end' => 'nullable|date|after_or_equal:apply_start',
        ]);
        $period->update($validated);
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil diupdate.');
    }

    public function destroy(AcademicPeriod $period)
    {
        if ($period->vacancies()->count() > 0 || \App\Models\Application::where('academic_period_id', $period->id)->count() > 0 || \App\Models\StudentRequirement::where('academic_period_id', $period->id)->count() > 0) {
            return redirect()->route('admin.periods.index')->with('error', 'Periode tidak dapat dihapus karena sudah memiliki data lowongan, pendaftaran, atau administrasi terkait.');
        }

        $period->delete();
        return redirect()->route('admin.periods.index')->with('success', 'Periode dihapus.');
    }

    public function truncate()
    {
        if (\App\Models\Vacancy::exists() || \App\Models\Application::exists() || \App\Models\StudentRequirement::exists()) {
            return redirect()->route('admin.periods.index')->with('error', 'Tidak dapat mengosongkan periode karena masih terdapat data lowongan, lamaran, atau administrasi mahasiswa terkait.');
        }

        AcademicPeriod::query()->delete();
        return redirect()->route('admin.periods.index')->with('success', 'Semua data periode berhasil dikosongkan.');
    }

    public function activate(AcademicPeriod $period)
    {
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        $period->update(['is_active' => true]);
        return back()->with('success', "Periode '{$period->name}' diaktifkan.");
    }
}
