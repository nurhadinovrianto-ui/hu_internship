<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use App\Models\Faculty;
use Illuminate\Http\Request;

class StudyProgramController extends Controller
{
    public function index(Request $request)
    {
        $query = StudyProgram::with('faculty')->withCount('students');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('head_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('degree')) {
            $query->where('degree', $request->degree);
        }

        $studyPrograms = $query->latest()->paginate(10)->withQueryString();
        $faculties = Faculty::where('status', 'active')->get();
        return view('admin.study-programs.index', compact('studyPrograms', 'faculties'));
    }

    public function create()
    {
        return redirect()->route('admin.study-programs.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:study_programs,code',
            'degree' => 'required|in:D3,D4,S1,S2,S3',
            'head_name' => 'nullable|string|max:255',
        ]);
        StudyProgram::create($validated);
        return redirect()->route('admin.study-programs.index')->with('success', 'Program Studi berhasil ditambah.');
    }

    public function show(StudyProgram $studyProgram)
    {
        return redirect()->route('admin.study-programs.edit', $studyProgram);
    }

    public function edit(StudyProgram $studyProgram)
    {
        $faculties = Faculty::where('status', 'active')->get();
        return view('admin.study-programs.edit', compact('studyProgram', 'faculties'));
    }

    public function update(Request $request, StudyProgram $studyProgram)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:study_programs,code,' . $studyProgram->id,
            'degree' => 'required|in:D3,D4,S1,S2,S3',
            'head_name' => 'nullable|string|max:255',
        ]);
        $studyProgram->update($validated);
        return redirect()->route('admin.study-programs.index')->with('success', 'Program Studi berhasil diupdate.');
    }

    public function destroy(StudyProgram $studyProgram)
    {
        if ($studyProgram->students()->count() > 0 || $studyProgram->lecturers()->count() > 0) {
            return redirect()->route('admin.study-programs.index')->with('error', 'Program Studi tidak dapat dihapus karena masih memiliki data mahasiswa atau dosen terkait.');
        }

        $studyProgram->delete();
        return redirect()->route('admin.study-programs.index')->with('success', 'Program Studi dihapus.');
    }
}
