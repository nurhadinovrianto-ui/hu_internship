<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Industry;
use App\Models\Internship;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentLocation;
use App\Models\StudentLocationLog;
use App\Models\StudyProgram;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Tampilkan halaman utama monitoring pelacakan mahasiswa realtime.
     */
    public function index()
    {
        $context = [
            'prefix'    => 'admin',
            'title'     => 'Admin',
            'homeRoute' => route('admin.dashboard'),
        ];
        $studyPrograms = StudyProgram::orderBy('name')->get();
        $faculties = Faculty::orderBy('name')->get();
        $industries = Industry::whereHas('vacancies.internships', function ($q) {
            $q->where('status', Internship::STATUS_ACTIVE);
        })->orderBy('name')->get();

        $geofenceRadius = (int) Setting::getValue('geofence_radius_meters', 500);

        return view('shared.tracking.index', compact(
            'context',
            'studyPrograms',
            'faculties',
            'industries',
            'geofenceRadius'
        ));
    }

    /**
     * Endpoint API pengembalian data realtime seluruh mahasiswa aktif untuk peta Leaflet.
     */
    public function liveData(Request $request)
    {
        $query = Internship::with([
            'student.user',
            'student.studyProgram',
            'student.location',
            'vacancy.industry',
            'dplAssignment.lecturer.user',
        ])->where('status', Internship::STATUS_ACTIVE);

        // Filter Program Studi
        if ($request->filled('study_program_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('study_program_id', $request->study_program_id);
            });
        }

        // Filter Perusahaan Mitra
        if ($request->filled('industry_id')) {
            $query->whereHas('vacancy', function ($q) use ($request) {
                $q->where('industry_id', $request->industry_id);
            });
        }

        // Pencarian Mahasiswa (Nama / NIM)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($qu) use ($search) {
                      $qu->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $internships = $query->get();

        $geofenceRadius = (int) Setting::getValue('geofence_radius_meters', 500);
        $today = now()->toDateString();

        $studentsData = [];
        $metrics = [
            'total'            => 0,
            'online'           => 0,
            'inside_geofence'  => 0,
            'outside_geofence' => 0,
            'offline'          => 0,
        ];

        foreach ($internships as $internship) {
            $student = $internship->student;
            if (!$student) continue;

            $user = $student->user;
            $industry = $internship->vacancy?->industry;
            $location = $student->location;

            // Cek presensi hari ini
            $todayAttendance = $internship->attendances()
                ->where('date', $today)
                ->first();

            // Tentukan status online / offline
            $isOnline = false;
            $lastSeenHuman = 'Belum pernah terlacak';
            $latitude = null;
            $longitude = null;
            $accuracy = null;
            $speed = null;
            $heading = null;
            $battery = null;

            if ($location && $location->latitude && $location->longitude) {
                $latitude = (float) $location->latitude;
                $longitude = (float) $location->longitude;
                $accuracy = $location->accuracy;
                $speed = $location->speed;
                $heading = $location->heading;
                $battery = $location->battery_level;

                if ($location->last_ping_at) {
                    $isOnline = $location->last_ping_at->greaterThanOrEqualTo(now()->subMinutes(2));
                    $lastSeenHuman = $location->last_ping_at->diffForHumans();
                }
            }

            // Hitung jarak ke lokasi industri & status geofence
            $distanceToIndustry = null;
            $isInsideGeofence = false;

            if ($latitude && $longitude && $industry && $industry->latitude && $industry->longitude) {
                $distanceToIndustry = $this->calculateDistance(
                    $latitude, $longitude,
                    (float) $industry->latitude, (float) $industry->longitude
                );

                $isInsideGeofence = ($distanceToIndustry <= $geofenceRadius);
            }

            // Hitung Metrik
            $metrics['total']++;
            if ($isOnline) {
                $metrics['online']++;
                if ($isInsideGeofence) {
                    $metrics['inside_geofence']++;
                } else {
                    $metrics['outside_geofence']++;
                }
            } else {
                $metrics['offline']++;
            }

            // Status filter post-query jika ada
            if ($request->filled('status')) {
                if ($request->status === 'online' && !$isOnline) continue;
                if ($request->status === 'offline' && $isOnline) continue;
                if ($request->status === 'inside_geofence' && (!$isOnline || !$isInsideGeofence)) continue;
                if ($request->status === 'outside_geofence' && (!$isOnline || $isInsideGeofence)) continue;
            }

            $photoUrl = $student->photo 
                ? asset('storage/' . $student->photo) 
                : asset('edumin/images/avatar/1.jpg');

            $studentsData[] = [
                'id'                   => $student->id,
                'internship_id'        => $internship->id,
                'name'                 => $user?->name ?? 'Mahasiswa',
                'nim'                  => $student->nim,
                'photo'                => $photoUrl,
                'study_program'        => $student->studyProgram?->name ?? '-',
                'industry'             => [
                    'id'        => $industry?->id,
                    'name'      => $industry?->name ?? 'Tempat Magang',
                    'address'   => $industry?->address,
                    'latitude'  => $industry?->latitude ? (float) $industry->latitude : null,
                    'longitude' => $industry?->longitude ? (float) $industry->longitude : null,
                    'geofence_radius' => $geofenceRadius,
                ],
                'dpl'                  => $internship->dplAssignment?->lecturer?->user?->name ?? '-',
                'location'             => [
                    'latitude'         => $latitude,
                    'longitude'        => $longitude,
                    'accuracy'         => $accuracy,
                    'speed'            => $speed,
                    'heading'          => $heading,
                    'battery_level'    => $battery,
                    'is_online'        => $isOnline,
                    'last_seen'        => $lastSeenHuman,
                    'last_ping_at'     => $location?->last_ping_at?->toIso8601String(),
                ],
                'distance_to_industry' => $distanceToIndustry ? round($distanceToIndustry) : null,
                'is_inside_geofence'   => $isInsideGeofence,
                'attendance_today'     => [
                    'has_checked_in'  => (bool) $todayAttendance?->check_in_time,
                    'has_checked_out' => (bool) $todayAttendance?->check_out_time,
                    'check_in_time'   => $todayAttendance?->check_in_time ? Carbon::parse($todayAttendance->check_in_time)->format('H:i') : null,
                    'check_out_time'  => $todayAttendance?->check_out_time ? Carbon::parse($todayAttendance->check_out_time)->format('H:i') : null,
                    'status'          => $todayAttendance?->status,
                ],
            ];
        }

        return response()->json([
            'success'     => true,
            'timestamp'   => now()->toIso8601String(),
            'metrics'     => $metrics,
            'students'    => $studentsData,
        ]);
    }

    /**
     * Mengambil riwayat titik rute perjalanan mahasiswa untuk digambar sebagai polyline.
     */
    public function studentHistory(Student $student, Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $logs = StudentLocationLog::where('student_id', $student->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at', 'asc')
            ->get(['latitude', 'longitude', 'accuracy', 'speed', 'created_at']);

        $points = $logs->map(function ($log) {
            return [
                'lat'        => (float) $log->latitude,
                'lng'        => (float) $log->longitude,
                'accuracy'   => $log->accuracy,
                'speed'      => $log->speed,
                'time'       => Carbon::parse($log->created_at)->format('H:i:s'),
                'created_at' => $log->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success'      => true,
            'student_name' => $student->user->name,
            'date'         => $date,
            'points_count' => $points->count(),
            'points'       => $points,
        ]);
    }

    /**
     * Hitung jarak dua titik GPS menggunakan Haversine Formula (dalam meter).
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // meter

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
}
