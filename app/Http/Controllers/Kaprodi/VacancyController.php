<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Models\Industry;
use App\Models\IndustrySupervisor;
use App\Models\AcademicPeriod;
use App\Models\Application;
use App\Models\Internship;
use App\Models\DplAssignment;
use App\Models\Setting;
use App\Notifications\InternshipStatusNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    private function getStudyProgram()
    {
        return auth()->user()->managedStudyProgram();
    }

    private function checkAuthorization(Vacancy $vacancy)
    {
        $prodi = $this->getStudyProgram();
        $isCreator = (int) $vacancy->created_by === (int) auth()->id();
        $isTargetProdi = $prodi && (int) $vacancy->study_program_id === (int) $prodi->id;

        if (!$isCreator && !$isTargetProdi) {
            abort(403, 'Anda tidak memiliki akses ke data lowongan ini.');
        }
    }

    public function index(Request $request)
    {
        $prodi = $this->getStudyProgram();
        $query = Vacancy::with(['industry', 'supervisor.user', 'studyProgram', 'applications'])
            ->withCount('applications')
            ->where(function ($q) use ($prodi) {
                $q->where('created_by', auth()->id());
                if ($prodi) {
                    $q->orWhere('study_program_id', $prodi->id);
                }
            })
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhereHas('industry', fn($ind) => $ind->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_published', true)
                      ->where('is_closed', false)
                      ->where('apply_deadline', '>=', now()->toDateString());
            } elseif ($request->status === 'closed') {
                $query->where(function ($q) {
                    $q->where('is_closed', true)
                      ->orWhere('apply_deadline', '<', now()->toDateString());
                });
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $vacancies = $query->paginate(15)->withQueryString();
        $industries = Industry::orderBy('name')->get();

        // Statistik lowongan kaprodi
        $stats = [
            'total' => (clone $query)->count(),
            'active' => Vacancy::where('created_by', auth()->id())->where('is_published', true)->where('is_closed', false)->where('apply_deadline', '>=', now()->toDateString())->count(),
            'closed' => Vacancy::where('created_by', auth()->id())->where(fn($q) => $q->where('is_closed', true)->orWhere('apply_deadline', '<', now()->toDateString()))->count(),
            'total_applicants' => Application::whereHas('vacancy', fn($v) => $v->where('created_by', auth()->id()))->count(),
        ];

        return view('kaprodi.vacancies.index', compact('vacancies', 'industries', 'prodi', 'stats'));
    }

    public function create()
    {
        $period = AcademicPeriod::getActive();
        $prodi = $this->getStudyProgram();
        $industries = Industry::orderBy('name')->get();
        $supervisors = IndustrySupervisor::with(['user', 'industry'])->get();

        return view('kaprodi.vacancies.create', compact('period', 'prodi', 'industries', 'supervisors'));
    }

    public function store(Request $request)
    {
        $prodi = $this->getStudyProgram();
        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->withInput()->with('error', 'Tidak ada periode akademik aktif. Harap hubungi administrator.');
        }

        $minDays = (int) Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = Carbon::today()->addDays($minDays)->format('Y-m-d');

        $validated = $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'industry_supervisor_id' => 'nullable|exists:industry_supervisors,id',
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'quota' => 'required|integer|min:1',
            'duration' => 'required|string|max:255',
            'apply_deadline' => 'required|date|after_or_equal:' . $minDate,
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'nullable|string|max:255',
            'target_scope' => 'required|in:prodi,all',
        ]);

        $studyProgramId = ($request->target_scope === 'all') ? null : $prodi?->id;

        Vacancy::create(array_merge($validated, [
            'academic_period_id' => $period->id,
            'study_program_id' => $studyProgramId,
            'created_by' => auth()->id(),
            'is_published' => true,
        ]));

        return redirect()->route('kaprodi.vacancies.index')->with('success', 'Lowongan magang berhasil dibuat dan dipublikasikan.');
    }

    public function show(Vacancy $vacancy)
    {
        return redirect()->route('kaprodi.vacancies.applicants', $vacancy);
    }

    public function edit(Vacancy $vacancy)
    {
        $this->checkAuthorization($vacancy);
        $prodi = $this->getStudyProgram();
        $industries = Industry::orderBy('name')->get();
        $supervisors = IndustrySupervisor::with(['user', 'industry'])->get();

        return view('kaprodi.vacancies.edit', compact('vacancy', 'prodi', 'industries', 'supervisors'));
    }

    public function update(Request $request, Vacancy $vacancy)
    {
        $this->checkAuthorization($vacancy);
        $prodi = $this->getStudyProgram();

        $minDays = (int) Setting::getValue('min_days_vacancy_deadline', 0);
        $minDate = Carbon::today()->addDays($minDays)->format('Y-m-d');

        $deadlineRule = ($request->apply_deadline == $vacancy->apply_deadline?->format('Y-m-d'))
            ? 'required|date'
            : 'required|date|after_or_equal:' . $minDate;

        $validated = $request->validate([
            'industry_id' => 'required|exists:industries,id',
            'industry_supervisor_id' => 'nullable|exists:industry_supervisors,id',
            'title' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'division' => 'nullable|string|max:255',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'quota' => 'required|integer|min:1',
            'duration' => 'required|string|max:255',
            'apply_deadline' => $deadlineRule,
            'work_type' => 'required|in:onsite,remote,hybrid',
            'location' => 'nullable|string|max:255',
            'target_scope' => 'required|in:prodi,all',
            'is_closed' => 'nullable|boolean',
        ]);

        $studyProgramId = ($request->target_scope === 'all') ? null : $prodi?->id;

        $updateData = array_merge($validated, [
            'study_program_id' => $studyProgramId,
            'is_closed' => $request->has('is_closed') ? (bool) $request->is_closed : $vacancy->is_closed,
        ]);

        $vacancy->update($updateData);

        return redirect()->route('kaprodi.vacancies.index')->with('success', 'Lowongan berhasil diperbarui.');
    }

    public function destroy(Vacancy $vacancy)
    {
        $this->checkAuthorization($vacancy);

        if ($vacancy->applications()->count() > 0) {
            return back()->with('error', 'Lowongan tidak dapat dihapus karena sudah memiliki pelamar. Anda dapat menggunakan fitur Tutup Lowongan.');
        }

        $vacancy->delete();
        return redirect()->route('kaprodi.vacancies.index')->with('success', 'Lowongan berhasil dihapus.');
    }

    public function toggleStatus(Vacancy $vacancy)
    {
        $this->checkAuthorization($vacancy);
        $vacancy->update(['is_closed' => !$vacancy->is_closed]);
        $msg = $vacancy->is_closed ? 'ditutup' : 'dibuka kembali';
        return back()->with('success', "Lowongan berhasil {$msg}.");
    }

    public function applicants(Request $request, Vacancy $vacancy)
    {
        $this->checkAuthorization($vacancy);

        $query = Application::with(['student.user', 'student.studyProgram', 'vacancy.industry'])
            ->where('vacancy_id', $vacancy->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($sq) use ($search) {
                $sq->where('nim', 'like', "%{$search}%")
                   ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $applicants = $query->latest()->paginate(15)->withQueryString();

        return view('kaprodi.vacancies.applicants', compact('vacancy', 'applicants'));
    }

    public function acceptApplicant(Request $request, Application $application)
    {
        $vacancy = $application->vacancy;
        $this->checkAuthorization($vacancy);

        $request->validate(['notes' => 'nullable|string|max:500']);

        if (!in_array($application->status, [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])) {
            return back()->with('error', 'Pelamar ini sudah diproses sebelumnya.');
        }

        if ($vacancy->remaining_quota <= 0) {
            return back()->with('error', 'Kuota lowongan magang ini sudah penuh.');
        }

        $application->update([
            'status' => Application::STATUS_INDUSTRY_ACCEPTED,
            'industry_notes' => $request->notes ?? 'Diterima oleh Kaprodi / Mitra.',
            'industry_reviewed_at' => now(),
        ]);

        // Cek apakah mahasiswa SUDAH diplot DPL pra-penempatan di awal periode
        $student = $application->student;
        $period = $application->academicPeriod ?? AcademicPeriod::getActive();
        $preAssignment = DplAssignment::where('student_id', $student->id)
            ->where('academic_period_id', $period->id)
            ->whereNull('internship_id')
            ->first();

        $internshipStatus = $preAssignment ? Internship::STATUS_ACTIVE : Internship::STATUS_WAITING_DPL;

        $internship = Internship::create([
            'application_id' => $application->id,
            'student_id' => $application->student_id,
            'vacancy_id' => $application->vacancy_id,
            'academic_period_id' => $application->academic_period_id,
            'status' => $internshipStatus,
            'start_date' => $vacancy->start_date ?? now()->toDateString(),
        ]);

        // Jika sudah ada DPL pra-penempatan, otomatis sambungkan DPL ke internship ini!
        if ($preAssignment) {
            $preAssignment->update(['internship_id' => $internship->id]);

            // Notify DPL
            $preAssignment->lecturer->user->notify(new InternshipStatusNotification(
                'Mahasiswa Bimbingan Diterima Magang',
                "Mahasiswa bimbingan Anda {$student->user->name} telah resmi diterima di {$vacancy->industry->name} ({$vacancy->position}). Magang kini berstatus Aktif.",
                route('dpl.students')
            ));
        }

        // Batalkan otomatis lamaran lain milik mahasiswa ini
        Application::where('student_id', $application->student_id)
            ->where('id', '!=', $application->id)
            ->whereIn('status', [Application::STATUS_PENDING, Application::STATUS_KAPRODI_APPROVED])
            ->update([
                'status' => 'cancelled_by_system',
                'industry_notes' => 'Otomatis dibatalkan oleh sistem karena mahasiswa telah diterima di lowongan lain.',
            ]);

        // Tutup lowongan jika kuota penuh
        if ($vacancy->fresh()->remaining_quota <= 0) {
            $vacancy->update(['is_closed' => true]);
        }

        $dplNotice = $preAssignment
            ? " Dosen DPL ({$preAssignment->lecturer->user->name}) yang telah ditetapkan sebelumnya otomatis terhubung dan magang langsung Aktif."
            : " Selanjutnya silakan plot DPL untuk mahasiswa ini di menu Plotting DPL.";

        $application->student->user->notify(new InternshipStatusNotification(
            'Selamat! Anda Diterima Magang',
            "Lamaran Anda untuk posisi {$vacancy->position} di {$vacancy->industry->name} telah diterima." . ($preAssignment ? " DPL Anda adalah {$preAssignment->lecturer->user->name}." : ""),
            route('student.applications.show', $application->id)
        ));

        return back()->with('success', "Pelamar {$student->user->name} berhasil diterima magang!{$dplNotice}");
    }
}
