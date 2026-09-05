<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Login Google gagal. Coba lagi.']);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'name' => $user->name ?? $googleUser->getName(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'status' => 'active',
            ]);
        }

        if (!$user->isActive()) {
            return redirect()->route('login')->withErrors(['email' => 'Akun Anda tidak aktif.']);
        }

        Auth::login($user, true);

        if ($user->getRoleNames()->isEmpty()) {
            return redirect()->route('auth.pending');
        }

        return redirect($user->getDashboardRoute());
    }

    public function pending()
    {
        return view('auth.pending');
    }
}
