<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

use App\Models\StudyProgram;

class PaymentController extends Controller
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
            if ($request->status === 'cleared') {
                $query->whereHas('requirements', function($q) use ($period) {
                    if ($period) $q->where('academic_period_id', $period->id);
                    $q->where('payment_cleared', true);
                });
            } elseif ($request->status === 'pending') {
                $query->where(function($q) use ($period) {
                    $q->whereDoesntHave('requirements', function($rq) use ($period) {
                        if ($period) $rq->where('academic_period_id', $period->id);
                    })->orWhereHas('requirements', function($rq) use ($period) {
                        if ($period) $rq->where('academic_period_id', $period->id);
                        $rq->where('payment_cleared', false);
                    });
                });
            }
        }

        $students = $query->paginate(25)->withQueryString();
        $studyPrograms = StudyProgram::orderBy('name')->get();

        return view('finance.payments.index', compact('students', 'period', 'studyPrograms'));
    }

    public function validatePayment(Student $student)
    {
        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->with('error', 'Tidak ada periode aktif.');
        }

        StudentRequirement::updateOrCreate(
            ['student_id' => $student->id, 'academic_period_id' => $period->id],
            [
                'payment_cleared' => true,
                'payment_verified_at' => now()->toDateString(),
                'payment_verified_by' => auth()->id(),
                'sks_minimum' => (int) \App\Models\Setting::getValue('min_sks', 110),
            ]
        );

        $studentName = $student->user?->name ?? 'Mahasiswa';
        return back()->with('success', "Pembayaran {$studentName} telah diverifikasi.");
    }

    public function revokePayment(Student $student)
    {
        $period = AcademicPeriod::getActive();
        if (!$period) return back()->with('error', 'Tidak ada periode aktif.');

        StudentRequirement::where(['student_id' => $student->id, 'academic_period_id' => $period->id])
            ->update([
                'payment_cleared' => false,
                'payment_verified_at' => null,
                'payment_verified_by' => null,
            ]);

        $studentName = $student->user?->name ?? 'Mahasiswa';
        return back()->with('success', "Verifikasi pembayaran {$studentName} dicabut.");
    }

    public function export()
    {
        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->with('error', 'Tidak ada periode aktif untuk diexport.');
        }

        $cleanPeriodName = str_replace(['/', '\\'], '-', $period->name);
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PaymentExport($period), 'Rekap_Pembayaran_' . $cleanPeriodName . '_' . date('Ymd_His') . '.xlsx');
    }
}
