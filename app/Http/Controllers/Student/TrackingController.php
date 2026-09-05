<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\StudentLocation;
use App\Models\StudentLocationLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TrackingController extends Controller
{
    /**
     * Menerima pembaruan koordinat GPS dari browser/perangkat mahasiswa (Realtime Ping).
     */
    public function ping(Request $request)
    {
        $user = auth()->user();
        $student = $user?->student;

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Profil mahasiswa tidak ditemukan.'
            ], 404);
        }

        // Dapatkan magang yang sedang aktif
        $activeInternship = $student->internships()
            ->where('status', Internship::STATUS_ACTIVE)
            ->first();

        if (!$activeInternship) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki program magang yang sedang aktif saat ini.'
            ], 403);
        }

        $validated = $request->validate([
            'latitude'      => 'required|numeric|between:-90,90',
            'longitude'     => 'required|numeric|between:-180,180',
            'accuracy'      => 'nullable|numeric|min:0',
            'speed'         => 'nullable|numeric|min:0',
            'heading'       => 'nullable|numeric|between:0,360',
            'battery_level' => 'nullable|integer|between:0,100',
        ]);

        $now = now();

        // 1. Update atau Buat record lokasi terkini di student_locations
        $location = StudentLocation::updateOrCreate(
            ['student_id' => $student->id],
            [
                'internship_id' => $activeInternship->id,
                'latitude'      => $validated['latitude'],
                'longitude'     => $validated['longitude'],
                'accuracy'      => $validated['accuracy'] ?? null,
                'speed'         => $validated['speed'] ?? null,
                'heading'       => $validated['heading'] ?? null,
                'battery_level' => $validated['battery_level'] ?? null,
                'status'        => 'online',
                'last_ping_at'  => $now,
            ]
        );

        // 2. Simpan jejak rute (breadcrumb log) dengan throttling (minimal jeda 1 menit atau pergerakan signifikan)
        $lastLog = StudentLocationLog::where('student_id', $student->id)
            ->latest('created_at')
            ->first();

        $shouldLog = true;
        if ($lastLog) {
            $secondsSinceLastLog = $now->diffInSeconds(Carbon::parse($lastLog->created_at));
            // Hanya log jika sudah lebih dari 55 detik sejak log terakhir
            if ($secondsSinceLastLog < 55) {
                $shouldLog = false;
            }
        }

        if ($shouldLog) {
            StudentLocationLog::create([
                'student_id'    => $student->id,
                'internship_id' => $activeInternship->id,
                'latitude'      => $validated['latitude'],
                'longitude'     => $validated['longitude'],
                'accuracy'      => $validated['accuracy'] ?? null,
                'speed'         => $validated['speed'] ?? null,
                'created_at'    => $now,
            ]);
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Koordinat berhasil disinkronkan.',
            'server_time' => $now->toIso8601String(),
            'logged'      => $shouldLog,
        ]);
    }
}
