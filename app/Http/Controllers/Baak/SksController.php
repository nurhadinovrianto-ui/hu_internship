<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

use App\Models\StudyProgram;

class SksController extends Controller
{
    public function index(Request $request)
    {
        $period = AcademicPeriod::getActive();
        $query = Student::with(['user', 'studyProgram',
            'requirements' => fn($q) => $period ? $q->where('academic_period_id', $period->id) : $q
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('study_program_id')) {
            $query->where('study_program_id', $request->study_program_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'eligible') {
                $query->whereHas('requirements', function($q) use ($period) {
                    if ($period) $q->where('academic_period_id', $period->id);
                    $q->where('sks_eligible', true);
                });
            } elseif ($request->status === 'pending') {
                $query->where(function($q) use ($period) {
                    $q->whereDoesntHave('requirements', function($rq) use ($period) {
                        if ($period) $rq->where('academic_period_id', $period->id);
                    })->orWhereHas('requirements', function($rq) use ($period) {
                        if ($period) $rq->where('academic_period_id', $period->id);
                        $rq->where('sks_eligible', false);
                    });
                });
            }
        }

        $students = $query->paginate(25)->withQueryString();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('baak.sks.index', compact('students', 'period', 'studyPrograms'));
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
