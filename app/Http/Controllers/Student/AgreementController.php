<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use Illuminate\Http\Request;

class AgreementController extends Controller
{
    public function index()
    {
        $student = auth()->user()->student;
        $internship = $student->internships()->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED])->first();

        if (!$internship) {
            return view('student.agreement.index', ['blocked' => true, 'reason' => 'Anda belum memiliki program magang aktif.']);
        }

        $agreement = $internship->agreement;

        return view('student.agreement.index', compact('internship', 'agreement'));
    }
}
