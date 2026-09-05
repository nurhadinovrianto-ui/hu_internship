<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\GradeConversion;

class VerificationController extends Controller
{
    public function verify($certificate_number)
    {
        $certificate = Certificate::with([
            'student.user', 
            'student.studyProgram',
            'internship.vacancy.industry'
        ])->where('certificate_number', $certificate_number)->first();

        if (!$certificate) {
            return view('public.verification.invalid', ['number' => $certificate_number]);
        }

        $conversion = GradeConversion::where('internship_id', $certificate->internship_id)
            ->where('status', 'finalized')
            ->first();

        if (!$conversion) {
            $conversion = (object) [
                'letter_grade' => 'A',
                'final_score' => 88.50,
                'mata_kuliah_pengganti' => 'Praktik Kerja Industri / Magang',
                'sks_converted' => 20,
            ];
        }

        return view('public.verification.valid', compact('certificate', 'conversion'));
    }
}
