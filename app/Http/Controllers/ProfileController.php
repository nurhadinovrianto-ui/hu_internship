<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user()->load(['student.studyProgram', 'lecturer.studyProgram']);
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:25',
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        // Aturan spesifik Mahasiswa
        if ($user->hasRole('mahasiswa') && $user->student) {
            $rules = array_merge($rules, [
                'address' => 'nullable|string|max:500',
                'emergency_contact' => 'nullable|string|max:50',
                'date_of_birth' => 'nullable|date',
                'gender' => 'nullable|in:L,P',
                'portfolio_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'github_url' => 'nullable|url|max:255',
                'skills' => 'nullable|string|max:500',
                'bio' => 'nullable|string|max:1000',
                'cv_file' => 'nullable|file|mimes:pdf|max:5120',
                'transcript_file' => 'nullable|file|mimes:pdf|max:5120',
            ]);
        }

        // Aturan spesifik DPL / Dosen
        if ($user->hasRole('dpl') && $user->lecturer) {
            $rules = array_merge($rules, [
                'nip' => 'nullable|string|max:30|unique:lecturers,nip,' . $user->lecturer->id,
                'nidn' => 'nullable|string|max:20|unique:lecturers,nidn,' . $user->lecturer->id,
                'position' => 'nullable|string|max:100',
                'specialization' => 'nullable|string|max:255',
                'office_room' => 'nullable|string|max:100',
                'scholar_url' => 'nullable|url|max:255',
                'sinta_url' => 'nullable|url|max:255',
                'linkedin_url' => 'nullable|url|max:255',
                'bio' => 'nullable|string|max:1000',
                'cv_file' => 'nullable|file|mimes:pdf|max:5120',
            ]);
        }

        $validated = $request->validate($rules);

        // 1. Update data User
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $request->phone;

        // Update password jika diisi
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        // Upload Avatar
        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar !== 'avatars/default.png') {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        // 2. Update data Mahasiswa
        if ($user->hasRole('mahasiswa') && $user->student) {
            $student = $user->student;

            if ($request->hasFile('cv_file')) {
                if ($student->cv_file) {
                    Storage::disk('public')->delete($student->cv_file);
                }
                $student->cv_file = $request->file('cv_file')->store('student_cvs', 'public');
            }

            if ($request->hasFile('transcript_file')) {
                if ($student->transcript_file) {
                    Storage::disk('public')->delete($student->transcript_file);
                }
                $student->transcript_file = $request->file('transcript_file')->store('student_transcripts', 'public');
            }

            $student->update([
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'portfolio_url' => $request->portfolio_url,
                'linkedin_url' => $request->linkedin_url,
                'github_url' => $request->github_url,
                'skills' => $request->skills,
                'bio' => $request->bio,
            ]);
        }

        // 3. Update data DPL (Dosen)
        if ($user->hasRole('dpl') && $user->lecturer) {
            $lecturer = $user->lecturer;

            if ($request->hasFile('cv_file')) {
                if ($lecturer->cv_file) {
                    Storage::disk('public')->delete($lecturer->cv_file);
                }
                $lecturer->cv_file = $request->file('cv_file')->store('lecturer_cvs', 'public');
            }

            $lecturer->update([
                'nip' => $request->nip,
                'nidn' => $request->nidn,
                'position' => $request->position,
                'specialization' => $request->specialization,
                'office_room' => $request->office_room,
                'scholar_url' => $request->scholar_url,
                'sinta_url' => $request->sinta_url,
                'linkedin_url' => $request->linkedin_url,
                'bio' => $request->bio,
            ]);
        }

        return redirect()->back()->with('success', 'Profil dan dokumen berhasil diperbarui.');
    }
}
