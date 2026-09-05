<?php

namespace App\Http\Controllers\Shared;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function join(Meeting $meeting)
    {
        // Security check
        $user = auth()->user();
        
        $canJoin = false;
        
        if ($user->hasRole(['super-admin', 'admin'])) {
            $canJoin = true;
        } elseif ($meeting->host_user_id == $user->id) {
            $canJoin = true;
        } elseif ($user->hasRole('mahasiswa') && $user->student) {
            $canJoin = $meeting->internships()->where('student_id', $user->student->id)->exists();
        } elseif ($user->hasRole('dpl') && $user->lecturer) {
            $canJoin = $meeting->internships()->whereHas('dplAssignment', fn($q) => $q->where('lecturer_id', $user->lecturer->id))->exists();
        } elseif ($user->hasRole('supervisor-industri') && $user->industrySupervisor) {
            $spv = $user->industrySupervisor;
            $canJoin = $meeting->internships()->whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $spv->id)->orWhere('industry_id', $spv->industry_id))->exists();
        } elseif ($user->hasRole('kaprodi')) {
            $prodi = $user->managedStudyProgram();
            $canJoin = $prodi ? $meeting->internships()->whereHas('student', fn($q) => $q->where('study_program_id', $prodi->id))->exists() : false;
        }

        if (!$canJoin) {
            abort(403, 'Anda tidak memiliki akses ke ruang pertemuan ini.');
        }

        return view('shared.meetings.room', compact('meeting'));
    }
}
