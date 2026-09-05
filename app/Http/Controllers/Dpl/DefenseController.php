<?php

namespace App\Http\Controllers\Dpl;

use App\Http\Controllers\Controller;
use App\Models\InternshipDefense;
use App\Models\InternshipDefenseScore;
use Illuminate\Http\Request;

class DefenseController extends Controller
{
    private function getLecturer()
    {
        return auth()->user()->lecturer;
    }

    public function index(Request $request)
    {
        $lecturer = $this->getLecturer();
        if (!$lecturer) {
            return redirect()->route('dashboard.redirect')->with('error', 'Akun Anda tidak terhubung ke data dosen.');
        }

        $defenses = InternshipDefense::with(['student.user', 'internship.vacancy.industry', 'examiner.user', 'supervisor.user', 'scores'])
            ->where(function ($q) use ($lecturer) {
                $q->where('examiner_lecturer_id', $lecturer->id)
                  ->orWhere('supervisor_lecturer_id', $lecturer->id);
            })
            ->latest('scheduled_date')
            ->paginate(15);

        return view('dpl.defenses.index', compact('defenses', 'lecturer'));
    }

    public function assess(InternshipDefense $defense)
    {
        $lecturer = $this->getLecturer();
        abort_unless(
            $defense->examiner_lecturer_id == $lecturer?->id || $defense->supervisor_lecturer_id == $lecturer?->id,
            403,
            'Anda bukan penguji atau pembimbing untuk sidang ini.'
        );

        $myScore = InternshipDefenseScore::where('defense_id', $defense->id)
            ->where('evaluator_id', auth()->id())
            ->first();

        $roleInDefense = ($defense->examiner_lecturer_id == $lecturer?->id) ? 'Dosen Penguji' : 'Dosen Pembimbing (DPL)';

        return view('dpl.defenses.assess', compact('defense', 'lecturer', 'myScore', 'roleInDefense'));
    }

    public function storeAssessment(Request $request, InternshipDefense $defense)
    {
        $lecturer = $this->getLecturer();
        abort_unless(
            $defense->examiner_lecturer_id == $lecturer?->id || $defense->supervisor_lecturer_id == $lecturer?->id,
            403,
            'Akses ditolak.'
        );

        $request->validate([
            'presentation_score' => 'required|numeric|min:0|max:100',
            'material_mastery_score' => 'required|numeric|min:0|max:100',
            'report_quality_score' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'status' => 'nullable|in:passed,revision,failed',
        ]);

        $evaluatorRole = ($defense->examiner_lecturer_id == $lecturer->id) ? 'examiner' : 'supervisor';
        $average = InternshipDefenseScore::calculateAverage(
            $request->presentation_score,
            $request->material_mastery_score,
            $request->report_quality_score
        );

        InternshipDefenseScore::updateOrCreate(
            [
                'defense_id' => $defense->id,
                'evaluator_id' => auth()->id(),
            ],
            [
                'evaluator_role' => $evaluatorRole,
                'presentation_score' => $request->presentation_score,
                'material_mastery_score' => $request->material_mastery_score,
                'report_quality_score' => $request->report_quality_score,
                'average_score' => $average,
                'notes' => $request->notes,
            ]
        );

        // Rekalkulasi nilai akhir gabungan jika penguji sudah memberi nilai
        $allScores = $defense->scores()->get();
        if ($allScores->isNotEmpty()) {
            $finalAvg = round($allScores->avg('average_score'), 2);
            
            // Konversi nilai angka ke huruf
            $letter = match (true) {
                $finalAvg >= 85 => 'A',
                $finalAvg >= 80 => 'A-',
                $finalAvg >= 75 => 'B+',
                $finalAvg >= 70 => 'B',
                $finalAvg >= 65 => 'B-',
                $finalAvg >= 60 => 'C+',
                $finalAvg >= 55 => 'C',
                default => 'D',
            };

            $newStatus = $request->status ?? ($finalAvg >= 60 ? 'passed' : 'failed');

            $defense->update([
                'final_score' => $finalAvg,
                'grade_letter' => $letter,
                'status' => $newStatus,
                'passed_at' => ($newStatus === 'passed') ? now() : null,
                'revision_notes' => $request->notes,
                'revision_deadline' => ($newStatus === 'revision') ? now()->addDays(7) : null,
            ]);
        }

        return redirect()->route('dpl.defenses.index')
            ->with('success', 'Nilai sidang ujian magang berhasil disimpan!');
    }

    public function beritaAcara(InternshipDefense $defense)
    {
        $defense->load(['student.user', 'student.studyProgram.faculty', 'internship.vacancy.industry', 'examiner.user', 'supervisor.user', 'scores.evaluator']);

        return view('dpl.defenses.berita-acara', compact('defense'));
    }
}
