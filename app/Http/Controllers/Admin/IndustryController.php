<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $query = Industry::withCount('vacancies');
        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('city', 'like', "%{$request->search}%");
        }
        $industries = $query->latest()->paginate(20)->withQueryString();
        return view('admin.industries.index', compact('industries'));
    }

    public function create() { return view('admin.industries.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry_type' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'nullable|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'partnership_status' => 'required|in:mou,moa,none',
            'mou_start_date' => 'nullable|date',
            'mou_end_date' => 'nullable|date|after_or_equal:mou_start_date',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        Industry::create($validated);

        return redirect()->route('admin.industries.index')->with('success', 'Industri berhasil ditambah.');
    }

    public function show(Industry $industry)
    {
        $industry->load('supervisors.user', 'vacancies.applications');
        return view('admin.industries.show', compact('industry'));
    }

    public function edit(Industry $industry) { return view('admin.industries.edit', compact('industry')); }

    public function update(Request $request, Industry $industry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry_type' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'partnership_status' => 'required|in:mou,moa,none',
        ]);
        $industry->update($validated);
        return redirect()->route('admin.industries.index')->with('success', 'Data industri berhasil diupdate.');
    }

    public function destroy(Industry $industry)
    {
        $industry->delete();
        return redirect()->route('admin.industries.index')->with('success', 'Industri dihapus.');
    }

    public function togglePartner(Industry $industry)
    {
        $industry->update(['is_active' => !$industry->is_active]);
        return back()->with('success', 'Status mitra berhasil diubah.');
    }
}
