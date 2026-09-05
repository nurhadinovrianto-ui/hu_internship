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

    public function index(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return view('student.attendance.index', ['blocked' => true, 'reason' => 'Anda tidak memiliki program magang aktif.', 'geofenceEnabled' => false]);
        }

        $query = $internship->attendances();

        if ($request->filled('month')) {
            $query->whereMonth('date', Carbon::parse($request->month)->month)
                  ->whereYear('date', Carbon::parse($request->month)->year);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('date')->paginate(15)->withQueryString();
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

        $safeName = str_replace(['/', '\\'], '-', $internship->student->user->name);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.attendance.export', compact('internship', 'attendances'));
        return $pdf->download('Rekap_Presensi_' . $safeName . '.pdf');
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
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
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

        $photoPath = $this->storeAttendancePhoto($request->file('photo'));

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
        abort_unless($attendance->internship_id == $internship?->id, 403);

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

        $attendance->update([
            'check_out_time' => $checkOutTime->toTimeString(),
            'check_out_lat' => $request->lat,
            'check_out_lng' => $request->lng,
            'work_duration_minutes' => $duration,
        ]);

        return back()->with('success', 'Check-out berhasil disimpan. Selamat beristirahat!');
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Radius bumi dalam meter

        $latFrom = deg2rad((float)$lat1);
        $lonFrom = deg2rad((float)$lon1);
        $latTo = deg2rad((float)$lat2);
        $lonTo = deg2rad((float)$lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }

    public function storeBackdate(Request $request)
    {
        $internship = $this->getActiveInternship();
        if (!$internship) {
            return back()->with('error', 'Anda tidak memiliki program magang aktif.');
        }

        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i|after:check_in_time',
            'location_type' => 'required|in:industry,campus',
            'photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:10240',
            'notes' => 'nullable|string|max:255',
        ]);

        $date = $request->date;

        // Cek jika sudah ada presensi di tanggal tersebut
        $exists = Attendance::where('internship_id', $internship->id)
            ->where('date', $date)
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Anda sudah memiliki catatan kehadiran untuk tanggal tersebut.');
        }

        $locationType = $request->location_type;
        $approvalStatus = 'approved';

        // Untuk presensi susulan, GPS di bypass. 
        // Namun jika ke industri, statusnya pending untuk diperiksa supervisor.
        if ($locationType === 'industry') {
            $approvalStatus = 'pending';
        }

        $photoPath = $this->storeAttendancePhoto($request->file('photo'));

        $checkInTime = Carbon::parse($date . ' ' . $request->check_in_time);
        $checkOutTime = Carbon::parse($date . ' ' . $request->check_out_time);
        $duration = (int) round($checkInTime->diffInMinutes($checkOutTime));

        Attendance::create([
            'internship_id' => $internship->id,
            'student_id' => $internship->student_id,
            'date' => $date,
            'check_in_time' => $request->check_in_time . ':00',
            'check_out_time' => $request->check_out_time . ':00',
            // Koordinat GPS dikosongkan karena susulan
            'check_in_lat' => null,
            'check_in_lng' => null,
            'check_out_lat' => null,
            'check_out_lng' => null,
            'check_in_photo' => $photoPath,
            'work_duration_minutes' => $duration,
            'status' => 'present',
            'notes' => 'Presensi Susulan: ' . $request->notes,
            'location_type' => $locationType,
            'approval_status' => $approvalStatus,
        ]);

        return back()->with('success', 'Presensi susulan berhasil disimpan.');
    }

    private function storeAttendancePhoto($file): string
    {
        $destinationPath = storage_path('app/public/attendance_photos');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $filename = 'attendance_' . uniqid() . '_' . time() . '.jpg';
        $targetFile = $destinationPath . '/' . $filename;

        // Auto-compress with GD if file exceeds 1.5MB (or from phone camera)
        if ($file->getSize() > 1536 * 1024 && extension_loaded('gd')) {
            $imageInfo = @getimagesize($file->getRealPath());
            if ($imageInfo) {
                $mime = $imageInfo['mime'];
                $src = match ($mime) {
                    'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($file->getRealPath()),
                    'image/png' => @imagecreatefrompng($file->getRealPath()),
                    'image/webp' => @imagecreatefromwebp($file->getRealPath()),
                    default => null,
                };

                if ($src) {
                    $width = imagesx($src);
                    $height = imagesy($src);
                    $maxDimension = 1600;

                    if ($width > $maxDimension || $height > $maxDimension) {
                        $ratio = min($maxDimension / $width, $maxDimension / $height);
                        $newWidth = (int)($width * $ratio);
                        $newHeight = (int)($height * $ratio);
                        $dst = imagecreatetruecolor($newWidth, $newHeight);
                        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                        imagejpeg($dst, $targetFile, 80);
                        imagedestroy($dst);
                    } else {
                        imagejpeg($src, $targetFile, 80);
                    }
                    imagedestroy($src);
                    return 'attendance_photos/' . $filename;
                }
            }
        }

        return $file->store('attendance_photos', 'public');
    }
}
