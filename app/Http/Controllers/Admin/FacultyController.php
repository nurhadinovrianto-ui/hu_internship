<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function index()
    {
        $faculties = Faculty::withCount('studyPrograms')->get();
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
        $faculty->delete();
        return redirect()->route('admin.faculties.index')->with('success', 'Fakultas berhasil dihapus.');
    }
}
