<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserImportController;
use App\Http\Controllers\Admin\FacultyController;
use App\Http\Controllers\Admin\StudyProgramController;
use App\Http\Controllers\Admin\AcademicPeriodController;
use App\Http\Controllers\Admin\IndustryController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TrackingController as AdminTrackingController;
use App\Http\Controllers\Student\TrackingController as StudentTrackingController;
use App\Http\Controllers\Finance\DashboardController as FinanceDashboard;
use App\Http\Controllers\Finance\PaymentController;
use App\Http\Controllers\Baak\DashboardController as BaakDashboard;
use App\Http\Controllers\Baak\SksController;
use App\Http\Controllers\Baak\GradeConversionController;
use App\Http\Controllers\Kaprodi\DashboardController as KaprodiDashboard;
use App\Http\Controllers\Kaprodi\ApplicationController as KaprodiApplicationController;
use App\Http\Controllers\Kaprodi\DplPlottingController;
use App\Http\Controllers\Kaprodi\ReportController as KaprodiReportController;
use App\Http\Controllers\Dekan\DashboardController as DekanDashboard;
use App\Http\Controllers\Dpl\DashboardController as DplDashboard;
use App\Http\Controllers\Dpl\LogbookController as DplLogbookController;
use App\Http\Controllers\Dpl\AssessmentController as DplAssessmentController;
use App\Http\Controllers\Dpl\ReportController as DplReportController;
use App\Http\Controllers\Industry\DashboardController as IndustryDashboard;
use App\Http\Controllers\Industry\VacancyController;
use App\Http\Controllers\Industry\ApplicantController;
use App\Http\Controllers\Industry\LogbookController as IndustryLogbookController;
use App\Http\Controllers\Industry\ReportController as IndustryReportController;
use App\Http\Controllers\Student\DashboardController as StudentDashboard;
use App\Http\Controllers\Student\ApplicationController as StudentApplicationController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\LogbookController as StudentLogbookController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\AgreementController as StudentAgreementController;
use App\Http\Controllers\Student\CertificateController;

/*
|--------------------------------------------------------------------------
| SIMANG - Sistem Informasi Magang
| Horizon University Indonesia
|--------------------------------------------------------------------------
*/

// ======================================================
// LANDING PAGE
// ======================================================
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard.redirect');
    }
    return redirect()->route('login');
})->name('home');

Route::get('/verify-certificate/{certificate_number}', [\App\Http\Controllers\Public\VerificationController::class, 'verify'])->name('verification.verify');

// ======================================================
// AUTH ROUTES
// ======================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    // Google OAuth
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Common Profile Routes
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    
    // Impersonate Routes
    Route::impersonate();
});

// Pending page (Google login tapi belum punya role)
Route::middleware('auth')->get('/pending', [GoogleController::class, 'pending'])->name('auth.pending');

// ======================================================
// NOTIFICATIONS ROUTES
// ======================================================
Route::middleware('auth')->group(function () {
    Route::get('/notifications/check', function () {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['unread_count' => 0, 'latest' => []]);
        }

        $unreadNotifications = $user->unreadNotifications;
        $latest = $unreadNotifications->take(5)->map(function ($notif) {
            return [
                'id' => $notif->id,
                'title' => $notif->data['title'] ?? 'Notifikasi Baru',
                'message' => $notif->data['message'] ?? '',
                'url' => url('notifications/' . $notif->id . '/read'),
                'time' => $notif->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $unreadNotifications->count(),
            'latest' => $latest,
        ]);
    })->name('notifications.check');

    Route::get('/notifications/{id}/read', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return redirect($notification->data['url'] ?? '/dashboard');
    });

    Route::get('/notifications/mark-all-read', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sudah dibaca.');
    });
});

// ======================================================
// ROLE-BASED DASHBOARD REDIRECT
// ======================================================
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->hasRole('super-admin')) return redirect()->route('admin.dashboard');
    if ($user->hasRole('finance')) return redirect()->route('finance.dashboard');
    if ($user->hasRole('baak')) return redirect()->route('baak.dashboard');
    if ($user->hasRole('kaprodi')) return redirect()->route('kaprodi.dashboard');
    if ($user->hasRole('dekan')) return redirect()->route('dekan.dashboard');
    if ($user->hasRole('dpl')) return redirect()->route('dpl.dashboard');
    if ($user->hasRole('supervisor-industri')) return redirect()->route('industry.dashboard');
    if ($user->hasRole('mahasiswa')) return redirect()->route('student.dashboard');
    return redirect()->route('auth.pending');
})->name('dashboard.redirect');

// ======================================================
// SUPER ADMIN ROUTES
// ======================================================
Route::middleware(['auth', 'role:super-admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

        // Users
        Route::get('users/template/{role}', [UserImportController::class, 'downloadTemplate'])->name('users.template');
        Route::post('users/import', [UserImportController::class, 'import'])->name('users.import');
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::patch('users/{user}/assign-role', [UserController::class, 'assignRole'])->name('users.assign-role');
        Route::patch('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

        // Faculties
        Route::resource('faculties', FacultyController::class);

        // Study Programs
        Route::resource('study-programs', StudyProgramController::class);

        // Academic Periods
        Route::delete('periods/truncate', [AcademicPeriodController::class, 'truncate'])->name('periods.truncate');
        Route::resource('periods', AcademicPeriodController::class);
        Route::patch('periods/{period}/activate', [AcademicPeriodController::class, 'activate'])->name('periods.activate');

        // Industries (Perusahaan Mitra)
        Route::resource('industries', IndustryController::class);
        Route::patch('industries/{industry}/toggle-partner', [IndustryController::class, 'togglePartner'])->name('industries.toggle-partner');

        // Vacancies (Lowongan)
        Route::resource('vacancies', \App\Http\Controllers\Admin\VacancyController::class);
        Route::patch('vacancies/{vacancy}/toggle-status', [\App\Http\Controllers\Admin\VacancyController::class, 'toggleStatus'])->name('vacancies.toggle-status');

        // Application Settings
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Realtime Student Tracking
        Route::get('/tracking', [AdminTrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-data', [AdminTrackingController::class, 'liveData'])->name('tracking.live-data');
        Route::get('/tracking/{student}/history', [AdminTrackingController::class, 'studentHistory'])->name('tracking.history');
    });

// ======================================================
// FINANCE ROUTES
// ======================================================
Route::middleware(['auth', 'role:finance'])
    ->prefix('finance')
    ->name('finance.')
    ->group(function () {
        Route::get('/dashboard', [FinanceDashboard::class, 'index'])->name('dashboard');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::patch('/payments/{student}/validate', [PaymentController::class, 'validatePayment'])->name('payments.validate');
        Route::patch('/payments/{student}/revoke', [PaymentController::class, 'revokePayment'])->name('payments.revoke');
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    });

// ======================================================
// BAAK ROUTES
// ======================================================
Route::middleware(['auth', 'role:baak'])
    ->prefix('baak')
    ->name('baak.')
    ->group(function () {
        Route::get('/dashboard', [BaakDashboard::class, 'index'])->name('dashboard');
        Route::get('/sks', [SksController::class, 'index'])->name('sks.index');
        Route::patch('/sks/{student}/update', [SksController::class, 'update'])->name('sks.update');
        Route::get('/grade-conversions', [GradeConversionController::class, 'index'])->name('grade-conversions.index');
        Route::get('/grade-conversions/export', [GradeConversionController::class, 'export'])->name('grade-conversions.export');
        Route::post('/grade-conversions/{internship}/store', [GradeConversionController::class, 'store'])->name('grade-conversions.store');
    });

// ======================================================
// KAPRODI ROUTES
// ======================================================
Route::middleware(['auth', 'role:kaprodi'])
    ->prefix('kaprodi')
    ->name('kaprodi.')
    ->group(function () {
        Route::get('/dashboard', [KaprodiDashboard::class, 'index'])->name('dashboard');
        Route::get('/applications', [KaprodiApplicationController::class, 'index'])->name('applications.index');
        Route::get('/applications/{application}', [KaprodiApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/applications/{application}/approve', [KaprodiApplicationController::class, 'approve'])->name('applications.approve');
        Route::get('/applications/{application}/letter', [\App\Http\Controllers\Student\LetterController::class, 'downloadPengantar'])->name('applications.letter');
        Route::patch('/applications/{application}/reject', [KaprodiApplicationController::class, 'reject'])->name('applications.reject');
        Route::get('/internships', [\App\Http\Controllers\Kaprodi\InternshipController::class, 'index'])->name('internships.index');
        Route::patch('/internships/{internship}/cancel', [\App\Http\Controllers\Kaprodi\InternshipController::class, 'cancel'])->name('internships.cancel');
        
        // Lowongan Magang Kaprodi
        Route::resource('vacancies', \App\Http\Controllers\Kaprodi\VacancyController::class);
        Route::patch('vacancies/{vacancy}/toggle-status', [\App\Http\Controllers\Kaprodi\VacancyController::class, 'toggleStatus'])->name('vacancies.toggle-status');
        Route::get('vacancies/{vacancy}/applicants', [\App\Http\Controllers\Kaprodi\VacancyController::class, 'applicants'])->name('vacancies.applicants');
        Route::patch('vacancies/applicants/{application}/accept', [\App\Http\Controllers\Kaprodi\VacancyController::class, 'acceptApplicant'])->name('vacancies.applicants.accept');

        // Plotting DPL
        Route::get('/dpl-plotting', [DplPlottingController::class, 'index'])->name('dpl-plotting.index');
        Route::post('/dpl-plotting/pre-placement/{student}/assign', [DplPlottingController::class, 'assignPrePlacement'])->name('dpl-plotting.pre-placement.assign');
        Route::delete('/dpl-plotting/pre-placement/{student}/remove', [DplPlottingController::class, 'removePrePlacement'])->name('dpl-plotting.pre-placement.remove');
        Route::post('/dpl-plotting/{internship}/assign', [DplPlottingController::class, 'assign'])->name('dpl-plotting.assign');
        Route::post('/dpl-plotting/{internship}/reassign', [DplPlottingController::class, 'reassign'])->name('dpl-plotting.reassign');
        Route::get('/reports', [KaprodiReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/receive', [KaprodiReportController::class, 'receive'])->name('reports.receive');
        Route::get('/statistics', [KaprodiDashboard::class, 'statistics'])->name('statistics');

        // Realtime Student Tracking
        Route::get('/tracking', [\App\Http\Controllers\Shared\TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-data', [\App\Http\Controllers\Shared\TrackingController::class, 'liveData'])->name('tracking.live-data');
        Route::get('/tracking/{student}/history', [\App\Http\Controllers\Shared\TrackingController::class, 'studentHistory'])->name('tracking.history');

        // Magang Mandiri
        Route::get('/self-proposals', [\App\Http\Controllers\Kaprodi\SelfProposalController::class, 'index'])->name('self-proposals.index');
        Route::get('/self-proposals/{proposal}', [\App\Http\Controllers\Kaprodi\SelfProposalController::class, 'show'])->name('self-proposals.show');
        Route::patch('/self-proposals/{proposal}/approve', [\App\Http\Controllers\Kaprodi\SelfProposalController::class, 'approve'])->name('self-proposals.approve');
        Route::patch('/self-proposals/{proposal}/reject', [\App\Http\Controllers\Kaprodi\SelfProposalController::class, 'reject'])->name('self-proposals.reject');
        Route::patch('/self-proposals/{proposal}/revision', [\App\Http\Controllers\Kaprodi\SelfProposalController::class, 'revision'])->name('self-proposals.revision');

        // Seminar / Sidang Ujian Magang
        Route::get('/defenses', [\App\Http\Controllers\Kaprodi\DefenseController::class, 'index'])->name('defenses.index');
        Route::post('/defenses/{defense}/schedule', [\App\Http\Controllers\Kaprodi\DefenseController::class, 'schedule'])->name('defenses.schedule');

        // Kuesioner Evaluasi Kepuasan
        Route::get('/surveys', [\App\Http\Controllers\Shared\SurveyController::class, 'analyticsIndex'])->name('surveys.index');
    });

// ======================================================
// DEKAN ROUTES
// ======================================================
Route::middleware(['auth', 'role:dekan'])
    ->prefix('dekan')
    ->name('dekan.')
    ->group(function () {
        Route::get('/dashboard', [DekanDashboard::class, 'index'])->name('dashboard');
        Route::get('/statistics', [DekanDashboard::class, 'statistics'])->name('statistics');
        Route::get('/industries', [DekanDashboard::class, 'industries'])->name('industries');
        Route::get('/internships', [DekanDashboard::class, 'internships'])->name('internships');

        // Realtime Student Tracking
        Route::get('/tracking', [\App\Http\Controllers\Shared\TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-data', [\App\Http\Controllers\Shared\TrackingController::class, 'liveData'])->name('tracking.live-data');
        Route::get('/tracking/{student}/history', [\App\Http\Controllers\Shared\TrackingController::class, 'studentHistory'])->name('tracking.history');

        // Kuesioner Evaluasi Kepuasan
        Route::get('/surveys', [\App\Http\Controllers\Shared\SurveyController::class, 'analyticsIndex'])->name('surveys.index');
    });

// ======================================================
// DPL (DOSEN PEMBIMBING LAPANGAN) ROUTES
// ======================================================
Route::middleware(['auth', 'role:dpl'])
    ->prefix('dpl')
    ->name('dpl.')
    ->group(function () {
        Route::get('/dashboard', [DplDashboard::class, 'index'])->name('dashboard');
        Route::get('/students', [DplDashboard::class, 'students'])->name('students');
        Route::get('/vacancies', [\App\Http\Controllers\Dpl\VacancyController::class, 'index'])->name('vacancies.index');
        Route::get('/vacancies/{vacancy}', [\App\Http\Controllers\Dpl\VacancyController::class, 'show'])->name('vacancies.show');
        Route::get('/logbooks', [DplLogbookController::class, 'index'])->name('logbooks.index');
        Route::get('/logbooks/{logbook}', [DplLogbookController::class, 'show'])->name('logbooks.show');
        Route::post('/logbooks/{logbook}/review', [DplLogbookController::class, 'review'])->name('logbooks.review');
        Route::get('/attendance', [\App\Http\Controllers\Dpl\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{attendance}', [\App\Http\Controllers\Dpl\AttendanceController::class, 'show'])->name('attendance.show');
        Route::get('/assessment', [DplAssessmentController::class, 'index'])->name('assessment.index');
        Route::post('/assessment/{internship}/store', [DplAssessmentController::class, 'store'])->name('assessment.store');
        Route::get('/reports', [DplReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/{report}/approve', [DplReportController::class, 'approve'])->name('reports.approve');
        Route::resource('meetings', \App\Http\Controllers\Dpl\MeetingController::class)->except(['show']);

        // Seminar / Sidang Ujian Magang
        Route::get('/defenses', [\App\Http\Controllers\Dpl\DefenseController::class, 'index'])->name('defenses.index');
        Route::get('/defenses/{defense}/assess', [\App\Http\Controllers\Dpl\DefenseController::class, 'assess'])->name('defenses.assess');
        Route::post('/defenses/{defense}/assess', [\App\Http\Controllers\Dpl\DefenseController::class, 'storeAssessment'])->name('defenses.storeAssessment');
        Route::get('/defenses/{defense}/berita-acara', [\App\Http\Controllers\Dpl\DefenseController::class, 'beritaAcara'])->name('defenses.beritaAcara');

        // Realtime Student Tracking
        Route::get('/tracking', [\App\Http\Controllers\Shared\TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-data', [\App\Http\Controllers\Shared\TrackingController::class, 'liveData'])->name('tracking.live-data');
        Route::get('/tracking/{student}/history', [\App\Http\Controllers\Shared\TrackingController::class, 'studentHistory'])->name('tracking.history');

        // Review Usulan Magang Mandiri
        Route::get('/self-proposals', [\App\Http\Controllers\Dpl\SelfProposalController::class, 'index'])->name('self-proposals.index');
        Route::get('/self-proposals/{proposal}', [\App\Http\Controllers\Dpl\SelfProposalController::class, 'show'])->name('self-proposals.show');
        Route::patch('/self-proposals/{proposal}/approve', [\App\Http\Controllers\Dpl\SelfProposalController::class, 'approve'])->name('self-proposals.approve');
        Route::patch('/self-proposals/{proposal}/revision', [\App\Http\Controllers\Dpl\SelfProposalController::class, 'revision'])->name('self-proposals.revision');
        Route::patch('/self-proposals/{proposal}/reject', [\App\Http\Controllers\Dpl\SelfProposalController::class, 'reject'])->name('self-proposals.reject');
    });

// ======================================================
// INDUSTRY (SUPERVISOR INDUSTRI) ROUTES
// ======================================================
Route::middleware(['auth', 'role:supervisor-industri'])
    ->prefix('industry')
    ->name('industry.')
    ->group(function () {
        Route::get('/dashboard', [IndustryDashboard::class, 'index'])->name('dashboard');
        Route::resource('vacancies', VacancyController::class);
        Route::patch('vacancies/{vacancy}/toggle-status', [VacancyController::class, 'toggleStatus'])->name('vacancies.toggle-status');
        Route::get('vacancies/{vacancy}/applicants', [ApplicantController::class, 'index'])->name('applicants.index');
        Route::patch('applicants/{application}/accept', [ApplicantController::class, 'accept'])->name('applicants.accept');
        Route::patch('applicants/{application}/reject', [ApplicantController::class, 'reject'])->name('applicants.reject');
        Route::get('/logbooks', [IndustryLogbookController::class, 'index'])->name('logbooks.index');
        Route::get('/logbooks/{logbook}', [IndustryLogbookController::class, 'show'])->name('logbooks.show');
        Route::post('/logbooks/{logbook}/review', [IndustryLogbookController::class, 'review'])->name('logbooks.review');
        Route::get('/reports', [IndustryReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/{report}', [IndustryReportController::class, 'show'])->name('reports.show');
        Route::post('/reports/{report}/approve', [IndustryReportController::class, 'approve'])->name('reports.approve');
        Route::get('/attendance', [\App\Http\Controllers\Industry\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{attendance}', [\App\Http\Controllers\Industry\AttendanceController::class, 'show'])->name('attendance.show');
        Route::patch('/attendance/{attendance}/approve', [\App\Http\Controllers\Industry\AttendanceController::class, 'approve'])->name('attendance.approve');
        Route::patch('/attendance/{attendance}/reject', [\App\Http\Controllers\Industry\AttendanceController::class, 'reject'])->name('attendance.reject');
        Route::get('/assessment', [IndustryDashboard::class, 'assessment'])->name('assessment.index');
        Route::post('/assessment/{internship}', [IndustryDashboard::class, 'storeAssessment'])->name('assessment.store');
        Route::get('/assessment-criteria', [\App\Http\Controllers\Industry\AssessmentCriterionController::class, 'index'])->name('assessment-criteria.index');
        Route::post('/assessment-criteria', [\App\Http\Controllers\Industry\AssessmentCriterionController::class, 'store'])->name('assessment-criteria.store');
        Route::put('/assessment-criteria/{criterion}', [\App\Http\Controllers\Industry\AssessmentCriterionController::class, 'update'])->name('assessment-criteria.update');
        Route::delete('/assessment-criteria/{criterion}', [\App\Http\Controllers\Industry\AssessmentCriterionController::class, 'destroy'])->name('assessment-criteria.destroy');
        Route::post('/assessment-criteria/customize', [\App\Http\Controllers\Industry\AssessmentCriterionController::class, 'customizeDefault'])->name('assessment-criteria.customize');
        Route::get('/certificates', [\App\Http\Controllers\Industry\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/template', [\App\Http\Controllers\Industry\CertificateController::class, 'template'])->name('certificates.template');
        Route::post('/certificates/template', [\App\Http\Controllers\Industry\CertificateController::class, 'updateTemplate'])->name('certificates.template.update');
        Route::post('/certificates/{internship}/generate', [\App\Http\Controllers\Industry\CertificateController::class, 'generate'])->name('certificates.generate');
        Route::post('/certificates/{internship}/upload', [\App\Http\Controllers\Industry\CertificateController::class, 'uploadManual'])->name('certificates.upload');
        Route::get('/certificates/{internship}/download', [\App\Http\Controllers\Industry\CertificateController::class, 'download'])->name('certificates.download');
        Route::resource('meetings', \App\Http\Controllers\Supervisor\MeetingController::class)->except(['show']);
        Route::get('/agreements/{internship}/template', [\App\Http\Controllers\Industry\AgreementController::class, 'template'])->name('agreements.template');
        Route::post('/agreements/{internship}', [\App\Http\Controllers\Industry\AgreementController::class, 'store'])->name('agreements.store');
        Route::resource('agreements', \App\Http\Controllers\Industry\AgreementController::class)->only(['index', 'destroy']);

        // Realtime Student Tracking
        Route::get('/tracking', [\App\Http\Controllers\Shared\TrackingController::class, 'index'])->name('tracking.index');
        Route::get('/tracking/live-data', [\App\Http\Controllers\Shared\TrackingController::class, 'liveData'])->name('tracking.live-data');
        Route::get('/tracking/{student}/history', [\App\Http\Controllers\Shared\TrackingController::class, 'studentHistory'])->name('tracking.history');

        // Kuesioner Evaluasi Kepuasan
        Route::get('/internships/{internship}/survey', [\App\Http\Controllers\Shared\SurveyController::class, 'industrySurveyForm'])->name('surveys.form');
        Route::post('/internships/{internship}/survey', [\App\Http\Controllers\Shared\SurveyController::class, 'storeIndustrySurvey'])->name('surveys.store');
    });

Route::middleware(['auth', 'role:supervisor-industri'])
    ->prefix('supervisor')
    ->name('supervisor.')
    ->group(function () {
        Route::get('/dashboard', [IndustryDashboard::class, 'index'])->name('dashboard');
        Route::resource('meetings', \App\Http\Controllers\Supervisor\MeetingController::class)->except(['show']);
    });

// ======================================================
// STUDENT (MAHASISWA) ROUTES
// ======================================================
Route::middleware(['auth', 'role:mahasiswa'])
    ->prefix('student')
    ->name('student.')
    ->group(function () {
        Route::get('/dashboard', [StudentDashboard::class, 'index'])->name('dashboard');

        // Lowongan & Apply
        Route::get('/vacancies', [StudentApplicationController::class, 'browse'])->name('vacancies.browse');
        Route::get('/vacancies/{vacancy}', [StudentApplicationController::class, 'showVacancy'])->name('vacancies.show');
        Route::post('/vacancies/{vacancy}/apply', [StudentApplicationController::class, 'apply'])->name('vacancies.apply');
        Route::get('/applications', [StudentApplicationController::class, 'myApplications'])->name('applications.index');
        Route::get('/applications/{application}', [StudentApplicationController::class, 'showApplication'])->name('applications.show');
        Route::get('/applications/{application}/letter', [\App\Http\Controllers\Student\LetterController::class, 'downloadPengantar'])->name('applications.letter');

        // Pengajuan Magang Mandiri
        Route::get('/self-proposals', [\App\Http\Controllers\Student\SelfProposalController::class, 'index'])->name('self-proposals.index');
        Route::get('/self-proposals/create', [\App\Http\Controllers\Student\SelfProposalController::class, 'create'])->name('self-proposals.create');
        Route::post('/self-proposals', [\App\Http\Controllers\Student\SelfProposalController::class, 'store'])->name('self-proposals.store');
        Route::get('/self-proposals/{proposal}', [\App\Http\Controllers\Student\SelfProposalController::class, 'show'])->name('self-proposals.show');
        Route::get('/self-proposals/{proposal}/edit', [\App\Http\Controllers\Student\SelfProposalController::class, 'edit'])->name('self-proposals.edit');
        Route::put('/self-proposals/{proposal}', [\App\Http\Controllers\Student\SelfProposalController::class, 'update'])->name('self-proposals.update');

        // Absensi
        Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::patch('/attendance/{attendance}/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
        Route::post('/attendance/backdate', [AttendanceController::class, 'storeBackdate'])->name('attendance.storeBackdate');

        // Logbook
        Route::get('/logbooks/export', [StudentLogbookController::class, 'export'])->name('logbooks.export');
        Route::resource('logbooks', StudentLogbookController::class)->except(['destroy']);

        // Laporan Akhir
        Route::get('/report', [ReportController::class, 'index'])->name('report.index');
        Route::post('/report', [ReportController::class, 'upload'])->name('report.upload');

        // Seminar / Sidang Ujian Magang
        Route::get('/defense', [\App\Http\Controllers\Student\DefenseController::class, 'index'])->name('defense.index');
        Route::post('/defense/register', [\App\Http\Controllers\Student\DefenseController::class, 'register'])->name('defense.register');

        // Internship Agreement
        Route::get('/agreement', [StudentAgreementController::class, 'index'])->name('agreement.index');

        // Sertifikat
        Route::get('/certificate', [CertificateController::class, 'index'])->name('certificate.index');
        Route::get('/certificate/download', [\App\Http\Controllers\Student\CertificateController::class, 'download'])->name('certificate.download');
        Route::get('/meetings', [\App\Http\Controllers\Student\MeetingController::class, 'index'])->name('meetings.index');

        // Kuesioner Evaluasi Mahasiswa
        Route::get('/internships/{internship}/survey', [\App\Http\Controllers\Shared\SurveyController::class, 'studentSurveyForm'])->name('surveys.form');
        Route::post('/internships/{internship}/survey', [\App\Http\Controllers\Shared\SurveyController::class, 'storeStudentSurvey'])->name('surveys.store');

        // Realtime Tracking Ping
        Route::post('/tracking/ping', [StudentTrackingController::class, 'ping'])->name('tracking.ping');
    });

// SHARED ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/meetings/{meeting}/join', [\App\Http\Controllers\Shared\MeetingRoomController::class, 'join'])->name('meetings.join');
});
