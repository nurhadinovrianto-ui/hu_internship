<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class SksController extends Controller
{
    public function index(Request $request)
    {
        $period = AcademicPeriod::getActive();
        $students = Student::with(['user', 'studyProgram',
            'requirements' => fn($q) => $period ? $q->where('academic_period_id', $period->id) : $q
        ])->paginate(25)->withQueryString();

        return view('baak.sks.index', compact('students', 'period'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'sks_completed' => 'required|integer|min:0|max:250',
            'sks_minimum' => 'required|integer|min:0|max:250',
        ]);

        $period = AcademicPeriod::getActive();
        if (!$period) return back()->with('error', 'Tidak ada periode aktif.');

        $req = StudentRequirement::updateOrCreate(
            ['student_id' => $student->id, 'academic_period_id' => $period->id],
            array_merge($validated, [
                'sks_verified_at' => now()->toDateString(),
                'sks_verified_by' => auth()->id(),
            ])
        );

        return back()->with('success', "Data SKS {$student->user->name} berhasil diupdate. Status: " . ($req->sks_eligible ? 'Eligible ✅' : 'Belum Eligible ❌'));
    }
}
