<?php

namespace App\Http\Controllers\Dekan;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Student;
use App\Models\Internship;
use App\Models\Industry;
use App\Models\Application;
use App\Models\StudyProgram;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $faculty = auth()->user()->managedFaculty();
        $facultyId = $faculty?->id;

        $stats = [
            'total_prodi' => StudyProgram::where('faculty_id', $facultyId)->count(),
            'total_students' => Student::whereHas('studyProgram', fn($q) => $q->where('faculty_id', $facultyId))->count(),
            'active_internships' => Internship::whereHas('student.studyProgram', fn($q) => $q->where('faculty_id', $facultyId))->where('status', 'active')->count(),
            'partner_industries' => Industry::where('is_active', true)->count(),
            'total_applications' => Application::whereHas('student.studyProgram', fn($q) => $q->where('faculty_id', $facultyId))->count(),
            'completed_internships' => Internship::whereHas('student.studyProgram', fn($q) => $q->where('faculty_id', $facultyId))->where('status', 'completed')->count(),
        ];

        $internshipsByProdi = Internship::with('student.studyProgram.faculty')
            ->whereHas('student.studyProgram', fn($q) => $q->where('faculty_id', $facultyId))
            ->where('status', 'active')
            ->get()
            ->groupBy(fn($i) => $i->student?->studyProgram?->name ?? 'Tidak Diketahui');

        $topIndustries = Industry::withCount(['vacancies as internship_count' => fn($q) =>
            $q->whereHas('applications', fn($a) => 
                $a->where('status', 'industry_accepted')
                  ->whereHas('student.studyProgram', fn($sp) => $sp->where('faculty_id', $facultyId))
            )
        ])->orderByDesc('internship_count')->limit(5)->get();

        return view('dekan.dashboard', compact('stats', 'internshipsByProdi', 'topIndustries', 'faculty'));
    }

    public function statistics()
    {
        $faculty = auth()->user()->managedFaculty();
        return view('dekan.statistics', compact('faculty'));
    }

    public function industries()
    {
        $faculty = auth()->user()->managedFaculty();
        $industries = Industry::withCount('vacancies')->latest()->get();
        return view('dekan.industries', compact('industries', 'faculty'));
    }

    public function internships(Request $request)
    {
        $faculty = auth()->user()->managedFaculty();
        $studyProgramsQuery = StudyProgram::orderBy('name');
        if ($faculty) {
            $studyProgramsQuery->where('faculty_id', $faculty->id);
        }
        $studyPrograms = $studyProgramsQuery->get();
        $industries = Industry::orderBy('name')->get();

        $query = Internship::with([
            'student.user',
            'student.studyProgram.faculty',
            'vacancy.industry',
            'vacancy.supervisor',
            'dplAssignment.lecturer.user',
            'agreement'
        ]);

        if ($faculty) {
            $query->whereHas('student.studyProgram', fn($q) => $q->where('faculty_id', $faculty->id));
        }

        if ($request->filled('study_program_id')) {
            $query->whereHas('student', fn($q) => $q->where('study_program_id', $request->study_program_id));
        }

        if ($request->filled('industry_id')) {
            $query->whereHas('vacancy', fn($q) => $q->where('industry_id', $request->industry_id));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('nim', 'like', "%{$search}%")
                       ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
                })->orWhereHas('vacancy.industry', function ($iq) use ($search) {
                    $iq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Statistik ringkasan
        $allFiltered = (clone $query)->get();
        $stats = [
            'total_interns' => $allFiltered->count(),
            'total_locations' => $allFiltered->pluck('vacancy.industry_id')->filter()->unique()->count(),
            'active_interns' => $allFiltered->where('status', 'active')->count(),
            'completed_interns' => $allFiltered->where('status', 'completed')->count(),
        ];

        $internships = $query->latest()->paginate(20)->withQueryString();

        return view('dekan.internships', compact('internships', 'studyPrograms', 'industries', 'stats', 'faculty'));
    }
}
