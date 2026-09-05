<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileCompleteness
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->hasRole('mahasiswa')) {
            $student = $user->student;
            if (!$student || !$student->nim || !$student->phone) {
                return redirect()->route('student.profile')->with('info', 'Silakan lengkapi data profil (NIM & No Telepon) Anda terlebih dahulu.');
            }
        }

        return $next($request);
    }
}
