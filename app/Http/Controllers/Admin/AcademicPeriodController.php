<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::latest()->get();
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
        $period->delete();
        return redirect()->route('admin.periods.index')->with('success', 'Periode dihapus.');
    }

    public function truncate()
    {
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
