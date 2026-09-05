<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipSurvey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    // ==========================================
    // 1. SURVEI MITRA INDUSTRI (SUPERVISOR)
    // ==========================================
    public function industrySurveyForm(Internship $internship)
    {
        $supervisor = auth()->user()->industrySupervisor;
        abort_unless($supervisor, 403, 'Akses ditolak.');

        $existing = InternshipSurvey::where('internship_id', $internship->id)
            ->where('respondent_type', 'industry')
            ->first();

        $questions = InternshipSurvey::getQuestions('industry');

        return view('shared.surveys.industry-form', compact('internship', 'existing', 'questions'));
    }

    public function storeIndustrySurvey(Request $request, Internship $internship)
    {
        $supervisor = auth()->user()->industrySupervisor;
        abort_unless($supervisor, 403, 'Akses ditolak.');

        $request->validate([
            'q1_rating' => 'required|integer|between:1,5',
            'q2_rating' => 'required|integer|between:1,5',
            'q3_rating' => 'required|integer|between:1,5',
            'q4_rating' => 'required|integer|between:1,5',
            'q5_rating' => 'required|integer|between:1,5',
            'feedback_text' => 'nullable|string|max:1000',
            'recommendation' => 'required|boolean',
        ]);

        $overall = round((
            $request->q1_rating +
            $request->q2_rating +
            $request->q3_rating +
            $request->q4_rating +
            $request->q5_rating
        ) / 5, 2);

        InternshipSurvey::updateOrCreate(
            [
                'internship_id' => $internship->id,
                'respondent_type' => 'industry',
            ],
            [
                'respondent_id' => auth()->id(),
                'q1_rating' => $request->q1_rating,
                'q2_rating' => $request->q2_rating,
                'q3_rating' => $request->q3_rating,
                'q4_rating' => $request->q4_rating,
                'q5_rating' => $request->q5_rating,
                'overall_score' => $overall,
                'feedback_text' => $request->feedback_text,
                'recommendation' => (bool) $request->recommendation,
            ]
        );

        return redirect()->route('industry.dashboard')
            ->with('success', 'Terima kasih! Kuesioner evaluasi kepuasan mitra industri berhasil disimpan.');
    }

    // ==========================================
    // 2. SURVEI MAHASISWA TERHADAP TEMPAT MAGANG
    // ==========================================
    public function studentSurveyForm(Internship $internship)
    {
        $student = auth()->user()->student;
        abort_unless($student && $internship->student_id == $student->id, 403, 'Akses ditolak.');

        $existing = InternshipSurvey::where('internship_id', $internship->id)
            ->where('respondent_type', 'student')
            ->first();

        $questions = InternshipSurvey::getQuestions('student');

        return view('shared.surveys.student-form', compact('internship', 'existing', 'questions'));
    }

    public function storeStudentSurvey(Request $request, Internship $internship)
    {
        $student = auth()->user()->student;
        abort_unless($student && $internship->student_id == $student->id, 403, 'Akses ditolak.');

        $request->validate([
            'q1_rating' => 'required|integer|between:1,5',
            'q2_rating' => 'required|integer|between:1,5',
            'q3_rating' => 'required|integer|between:1,5',
            'q4_rating' => 'required|integer|between:1,5',
            'q5_rating' => 'required|integer|between:1,5',
            'feedback_text' => 'nullable|string|max:1000',
            'recommendation' => 'required|boolean',
        ]);

        $overall = round((
            $request->q1_rating +
            $request->q2_rating +
            $request->q3_rating +
            $request->q4_rating +
            $request->q5_rating
        ) / 5, 2);

        InternshipSurvey::updateOrCreate(
            [
                'internship_id' => $internship->id,
                'respondent_type' => 'student',
            ],
            [
                'respondent_id' => auth()->id(),
                'q1_rating' => $request->q1_rating,
                'q2_rating' => $request->q2_rating,
                'q3_rating' => $request->q3_rating,
                'q4_rating' => $request->q4_rating,
                'q5_rating' => $request->q5_rating,
                'overall_score' => $overall,
                'feedback_text' => $request->feedback_text,
                'recommendation' => (bool) $request->recommendation,
            ]
        );

        return redirect()->route('student.dashboard')
            ->with('success', 'Terima kasih! Kuesioner evaluasi pengalaman magang berhasil disimpan.');
    }

    // ==========================================
    // 3. DASHBOARD ANALITIK KAPRODI & DEKAN
    // ==========================================
    public function analyticsIndex(Request $request)
    {
        $user = auth()->user();
        $isDekan = $user->hasRole('dekan');
        $prodi = $isDekan ? null : $user->managedStudyProgram();

        $query = InternshipSurvey::with(['internship.student.user', 'internship.student.studyProgram', 'internship.vacancy.industry', 'respondent']);

        if ($prodi) {
            $query->whereHas('internship.student', fn($q) => $q->where('study_program_id', $prodi->id));
        }

        $type = $request->get('type', 'industry');
        $surveys = (clone $query)->where('respondent_type', $type)->latest()->paginate(15);

        // Agregasi metrik rata-rata kepuasan
        $industryStats = (clone $query)->where('respondent_type', 'industry');
        $studentStats = (clone $query)->where('respondent_type', 'student');

        $avgIndustry = round($industryStats->avg('overall_score') ?? 0, 2);
        $avgStudent = round($studentStats->avg('overall_score') ?? 0, 2);

        $industryCount = $industryStats->count();
        $studentCount = $studentStats->count();

        // Rekomendasi %
        $recommendRateIndustry = $industryCount > 0 
            ? round(($industryStats->where('recommendation', true)->count() / $industryCount) * 100, 1) 
            : 100;

        $stats = [
            'avg_industry' => $avgIndustry,
            'avg_student' => $avgStudent,
            'count_industry' => $industryCount,
            'count_student' => $studentCount,
            'recommend_rate' => $recommendRateIndustry,
        ];

        return view('shared.surveys.analytics', compact('surveys', 'stats', 'type', 'isDekan', 'prodi'));
    }
}
