<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudyProgram;
use App\Models\Faculty;
use Illuminate\Http\Request;

class StudyProgramController extends Controller
{
    public function index()
    {
        $studyPrograms = StudyProgram::with('faculty')->withCount('students')->get();
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
        $studyProgram->delete();
        return redirect()->route('admin.study-programs.index')->with('success', 'Program Studi dihapus.');
    }
}
