<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::withCount('studyPrograms');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('dean_name', 'like', "%{$search}%");
            });
        }

        $faculties = $query->latest()->paginate(10)->withQueryString();
        return view('admin.faculties.index', compact('faculties'));
    }

    public function create() { return redirect()->route('admin.faculties.index'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code',
            'dean_name' => 'nullable|string|max:255',
        ]);
        Faculty::create($validated);
        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil ditambah.');
    }

    public function show(Faculty $faculty) { return redirect()->route('admin.faculties.edit', $faculty); }

    public function edit(Faculty $faculty) { return view('admin.faculties.edit', compact('faculty')); }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:faculties,code,' . $faculty->id,
            'dean_name' => 'nullable|string|max:255',
        ]);
        $faculty->update($validated);
        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil diupdate.');
    }

    public function destroy(Faculty $faculty)
    {
        if ($faculty->studyPrograms()->count() > 0) {
            return redirect()->route('admin.faculties.index')->with('error', 'Fakultas tidak dapat dihapus karena masih memiliki program studi terkait.');
        }

        $faculty->delete();
        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
