<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Internship;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    private function getActiveInternship()
    {
        $student = auth()->user()->student;
        return $student ? $student->internships()->where('status', Internship::STATUS_ACTIVE)->first() : null;
    }

    public function index()
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return view('student.attendance.index', ['blocked' => true, 'reason' => 'Anda tidak memiliki program magang aktif.', 'geofenceEnabled' => false]);
        }

        $attendances = $internship->attendances()->latest('date')->paginate(15);
        $todayAttendance = $internship->attendances()->where('date', now()->toDateString())->first();

        $maxRadius = (int) \App\Models\Setting::getValue('geofence_radius_meters', 0);
        $geofenceEnabled = $maxRadius > 0 && (\App\Models\Setting::getValue('use_industry_geofencing', '0') == '1');

        return view('student.attendance.index', compact('internship', 'attendances', 'todayAttendance', 'geofenceEnabled'));
    }

    public function export(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $attendances = $internship->attendances()->orderBy('date', 'asc')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.attendance.export', compact('internship', 'attendances'));
        return $pdf->download('Rekap_Presensi_' . $internship->student->user->name . '.pdf');
    }

    public function checkIn(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $today = now()->toDateString();

        // Cek jika sudah check in hari ini
        $exists = Attendance::where('internship_id', $internship->id)
            ->where('date', $today)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah melakukan check-in hari ini.');
        }

        $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'photo' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'notes' => 'nullable|string|max:255',
            'location_type' => 'required|in:industry,campus',
        ]);

        $locationType = $request->location_type;
        $maxRadius = (int) \App\Models\Setting::getValue('geofence_radius_meters', 0);
        $approvalStatus = 'approved'; // Default untuk kampus

        // GPS Geofencing: hanya ambil koordinat dari tempat magang (industri), jangan di kampus
        if ($locationType === 'industry') {
            $approvalStatus = 'pending'; // Butuh approval industri
            $useIndustryGeofencing = \App\Models\Setting::getValue('use_industry_geofencing', '0');
            if ($useIndustryGeofencing == '1' && $maxRadius > 0) {
                $industry = $internship->vacancy->industry;
                if ($industry && $industry->latitude && $industry->longitude && $request->filled('lat') && $request->filled('lng')) {
                    $distance = $this->calculateDistance(
                        $request->lat, $request->lng,
                        $industry->latitude, $industry->longitude
                    );
                    
                    if ($distance > $maxRadius) {
                        return back()->with('error', 'Check-in ditolak. Anda berada di luar area tempat magang (' . round($distance) . ' meter dari lokasi magang).');
                    }
                } elseif (!$request->filled('lat') || !$request->filled('lng')) {
                    return back()->with('error', 'Check-in ditolak. Koordinat GPS diperlukan karena pembatasan radius tempat magang aktif.');
                }
            }
        } elseif ($locationType === 'campus') {
            // Absensi ke kampus bebas pembatasan radius (tidak dicek koordinat kampus)
            $approvalStatus = 'approved';
        }

        $photoPath = $request->file('photo')->store('attendance_photos', 'public');

        Attendance::create([
            'internship_id' => $internship->id,
            'student_id' => $internship->student_id,
            'date' => $today,
            'check_in_time' => now()->toTimeString(),
            'check_in_lat' => $request->lat,
            'check_in_lng' => $request->lng,
            'check_in_photo' => $photoPath,
            'status' => 'present',
            'notes' => $request->notes,
            'location_type' => $locationType,
            'approval_status' => $approvalStatus,
        ]);

        return back()->with('success', 'Check-in berhasil disimpan.');
    }

    public function checkOut(Request $request, Attendance $attendance)
    {
        $internship = $this->getActiveInternship();
        // dd( (int) $attendance->internship_id, (int) $internship?->id);
        abort_unless( ( (int) $attendance->internship_id) ===  ((int) $internship?->id), 403);

        if ($attendance->check_out_time) {
            return back()->with('error', 'Anda sudah melakukan check-out hari ini.');
        }

        $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $checkInTime = Carbon::parse($attendance->check_in_time);
        $checkOutTime = now();
        $duration = (int) round($checkInTime->diffInMinutes($checkOutTime));
        // dd($checkOutTime->toTimeString(), $request->lat, $request->lng, $duration);
        $attendance->update([
            'check_out_time' => $checkOutTime->toTimeString(),
            'check_out_lat' => $request->lat,
            'check_out_lng' => $request->lng,
            'work_duration_minutes' => $duration,
        ]);
        // dd($attendance);

        return back()->with('success', 'Check-out berhasil disimpan. Selamat beristirahat!');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
