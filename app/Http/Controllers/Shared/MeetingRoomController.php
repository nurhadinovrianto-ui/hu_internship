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
        
        if ( ((int) $meeting->host_user_id) === ((int) $user->id)) {
            $canJoin = true;
        } elseif ($user->hasRole('mahasiswa') && $user->student) {
            $canJoin = $meeting->internships()->where('student_id', $user->student->id)->exists();
        }

        if (!$canJoin) {
            abort(403, 'Anda tidak memiliki akses ke ruang pertemuan ini.');
        }

        return view('shared.meetings.room', compact('meeting'));
    }
}
