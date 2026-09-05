<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentRequirement;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $period = AcademicPeriod::getActive();

        $query = Student::with(['user', 'studyProgram',
            'requirements' => fn($q) => $period ? $q->where('academic_period_id', $period->id) : $q
        ]);

        if ($request->search) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%{$request->search}%"))
                  ->orWhere('nim', 'like', "%{$request->search}%");
        }

        if ($request->status === 'cleared') {
            $query->whereHas('requirements', fn($q) => $q->where('payment_cleared', true));
        } elseif ($request->status === 'pending') {
            $query->whereHas('requirements', fn($q) => $q->where('payment_cleared', false));
        }

        $students = $query->paginate(25)->withQueryString();

        return view('finance.payments.index', compact('students', 'period'));
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

        return back()->with('success', "Pembayaran {$student->user->name} telah diverifikasi.");
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

        return back()->with('success', "Verifikasi pembayaran {$student->user->name} dicabut.");
    }

    public function export()
    {
        $period = AcademicPeriod::getActive();
        if (!$period) {
            return back()->with('error', 'Tidak ada periode aktif untuk diexport.');
        }

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PaymentExport($period), 'Rekap_Pembayaran_' . $period->name . '_' . date('Ymd_His') . '.xlsx');
    }
}
