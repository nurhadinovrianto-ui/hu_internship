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
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password if provided
        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($validated['password']);
        }

        // Handle Avatar Upload
        if ($request->hasFile('avatar')) {
            if ($user->avatar && $user->avatar !== 'avatars/default.png') {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }
}
