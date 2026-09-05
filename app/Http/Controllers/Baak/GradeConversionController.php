<?php

namespace App\Http\Controllers\Baak;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\GradeConversion;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\GradeExport;

class GradeConversionController extends Controller
{
    public function index()
    {
        $internships = Internship::with(['student.user', 'vacancy.industry', 'gradeConversion', 'assessments'])
            ->where('status', 'completed')
            ->paginate(25);

        return view('baak.grade-conversions.index', compact('internships'));
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
