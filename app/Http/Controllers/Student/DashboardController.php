<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use App\Models\StudentRequirement;
use App\Models\Application;
use App\Models\Internship;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getStudent()
    {
        return auth()->user()->student;
    }

    public function index()
    {
        $student = $this->getStudent();
        if (!$student) {
            return redirect()->route('auth.pending');
        }

        $period = AcademicPeriod::getActive();
        $requirement = $period ? $student->requirements()->where('academic_period_id', $period->id)->first() : null;

        $stats = [
            'has_active_internship' => $student->internships()->where('status', Internship::STATUS_ACTIVE)->exists(),
            'active_internship' => $student->internships()->where('status', Internship::STATUS_ACTIVE)->first(),
            'applications_count' => $student->applications()->count(),
            'attendance_today' => $student->attendances()->where('date', now()->toDateString())->first(),
            'certificate_available' => $student->certificates()->exists(),
        ];

        $applications = $student->applications()
            ->with(['vacancy.industry'])
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact('student', 'period', 'requirement', 'stats', 'applications'));
    }

    public function profile()
    {
        $student = $this->getStudent();
        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = $this->getStudent();
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $studentData = [
            'address' => $validated['address'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'emergency_contact' => $validated['emergency_contact'],
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('student_photos', 'public');
            $studentData['photo'] = $path;
        }

        $student->update($studentData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
