<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\GradeConversion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradeExport;

use App\Models\StudyProgram;

class GradeConversionController extends Controller
{
    public function index(Request $request)
    {
        $query = Internship::with(['student.user', 'student.studyProgram', 'vacancy.industry', 'gradeConversion', 'assessments'])
            ->where('status', 'completed');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy.industry', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('study_program_id')) {
            $query->whereHas('student', fn($sq) => $sq->where('study_program_id', $request->study_program_id));
        }

        if ($request->filled('status')) {
            if ($request->status === 'converted') {
                $query->whereHas('gradeConversion');
            } elseif ($request->status === 'pending') {
                $query->whereDoesntHave('gradeConversion');
            }
        }

        $internships = $query->latest()->paginate(25)->withQueryString();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('baak.grade-conversions.index', compact('internships', 'studyPrograms'));
    }

    public function store(Request $request, Internship $internship)
    {
        $validated = $request->validate([
            'sks_converted' => 'required|integer|min:1|max:24',
            'mata_kuliah_pengganti' => 'required|string|max:255',
        ]);

        $dplAssessment = $internship->assessments()->where('assessor_type', 'dpl')->first();
        $industryAssessment = $internship->assessments()->where('assessor_type', 'industry')->first();

        if (!$dplAssessment || !$industryAssessment) {
            return back()->with('error', 'Nilai DPL dan Industri harus sudah diinput terlebih dahulu.');
        }

        $dplScore = $dplAssessment->final_score;
        $industryScore = $industryAssessment->final_score;
        
        $weightIndustry = (float) \App\Models\Setting::getValue('grade_weight_industry', 40) / 100;
        $weightDpl = (float) \App\Models\Setting::getValue('grade_weight_dpl', 60) / 100;

        $finalScore = ($industryScore * $weightIndustry) + ($dplScore * $weightDpl);

        $letterGrade = match(true) {
            $finalScore >= 85 => 'A',
            $finalScore >= 80 => 'A-',
            $finalScore >= 75 => 'B+',
            $finalScore >= 70 => 'B',
            $finalScore >= 65 => 'B-',
            $finalScore >= 60 => 'C+',
            default => 'C',
        };

        $gradePoint = match($letterGrade) {
            'A' => 4.00, 'A-' => 3.75, 'B+' => 3.50, 'B' => 3.00,
            'B-' => 2.75, 'C+' => 2.50, default => 2.00,
        };

        GradeConversion::updateOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $internship->student_id,
                'industry_score' => $industryScore,
                'dpl_score' => $dplScore,
                'final_score' => round($finalScore, 2),
                'letter_grade' => $letterGrade,
                'grade_point' => $gradePoint,
                'sks_converted' => $validated['sks_converted'],
                'mata_kuliah_pengganti' => $validated['mata_kuliah_pengganti'],
                'processed_by' => auth()->id(),
                'processed_at' => now(),
                'status' => 'finalized',
            ]
        );

        return back()->with('success', "Konversi nilai berhasil. Nilai: {$letterGrade} ({$gradePoint})");
    }

    public function export()
    {
        return Excel::download(new GradeExport, 'Rekap_Nilai_Magang_' . date('Ymd_His') . '.xlsx');
    }
}
