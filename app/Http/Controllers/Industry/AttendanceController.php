<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            return redirect()->route('dashboard.redirect')->with('error', 'Akses ditolak.');
        }

        $query = Attendance::with(['student.user', 'internship.vacancy'])
            ->whereHas('internship.vacancy', function ($q) use ($supervisor) {
                $q->where('industry_supervisor_id', $supervisor->id);
            });

        if ($request->search) {
            $query->whereHas('student.user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }
        
        if ($request->date) {
            $query->where('date', $request->date);
        }

        $attendances = $query->latest('date')->paginate(15)->withQueryString();

        return view('industry.attendance.index', compact('attendances'));
    }

    public function show(Attendance $attendance)
    {
        $supervisor = $this->getSupervisor();
        abort_unless(((int) $attendance->internship->vacancy->industry_supervisor_id) === ((int) $supervisor->id), 403);

        $attendance->load(['student.user', 'internship.vacancy.industry']);

        return view('industry.attendance.show', compact('attendance'));
    }

    public function approve(Attendance $attendance)
    {
        $supervisor = $this->getSupervisor();
        abort_unless(((int) $attendance->internship->vacancy->industry_supervisor_id) === ((int) $supervisor->id), 403);

        $attendance->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id()
        ]);

        return back()->with('success', 'Absensi berhasil disetujui.');
    }

    public function reject(Request $request, Attendance $attendance)
    {
        $supervisor = $this->getSupervisor();
        abort_unless(((int) $attendance->internship->vacancy->industry_supervisor_id) === ((int) $supervisor->id), 403);

        $attendance->update([
            'approval_status' => 'rejected',
            'status' => 'absent', // Kalau ditolak, dianggap tidak hadir atau tergantung kebijakan
            'approved_by' => auth()->id(),
            'notes' => $attendance->notes . ' [Ditolak: ' . $request->reason . ']'
        ]);

        return back()->with('success', 'Absensi berhasil ditolak.');
    }
}
