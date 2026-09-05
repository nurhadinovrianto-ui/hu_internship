<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\Industry;
use App\Models\IndustrySupervisor;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->role($request->role);
        }

        $users = $query->paginate(20)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        $faculties = Faculty::where('status', 'active')->get();
        $studyPrograms = StudyProgram::where('status', 'active')->get();
        $industries = Industry::all();
        return view('admin.users.create', compact('roles', 'faculties', 'studyPrograms', 'industries'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ];

        if ($request->role === 'mahasiswa') {
            $rules['study_program_id'] = 'required|exists:study_programs,id';
            $rules['nim'] = 'required|string|max:30|unique:students,nim';
            $rules['batch'] = 'nullable|string|max:10';
            $rules['current_semester'] = 'required|integer|min:1';
            $rules['total_sks'] = 'required|integer|min:0';
            $rules['gpa'] = 'required|numeric|min:0|max:4';
        } elseif ($request->role === 'dpl') {
            $rules['study_program_id'] = 'required|exists:study_programs,id';
            $rules['nip'] = 'nullable|string|max:30|unique:lecturers,nip';
            $rules['nidn'] = 'nullable|string|max:20|unique:lecturers,nidn';
            $rules['position'] = 'nullable|string|max:255';
            $rules['specialization'] = 'nullable|string|max:255';
            $rules['max_mentee'] = 'required|integer|min:1';
        } elseif ($request->role === 'kaprodi') {
            $rules['leader_study_program_id'] = 'required|exists:study_programs,id';
        } elseif ($request->role === 'dekan') {
            $rules['leader_faculty_id'] = 'required|exists:faculties,id';
        } elseif ($request->role === 'supervisor-industri') {
            $rules['industry_id'] = 'required|exists:industries,id';
            $rules['position'] = 'nullable|string|max:255';
            $rules['division'] = 'nullable|string|max:255';
        }

        $messages = [
            'password.confirmed' => 'Kata sandi dan konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
        ];

        $validated = $request->validate($rules, $messages);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'status' => 'active',
        ]);

        $user->assignRole($validated['role']);

        if ($validated['role'] === 'mahasiswa') {
            $user->student()->create([
                'study_program_id' => $validated['study_program_id'],
                'nim' => $validated['nim'],
                'batch' => $validated['batch'] ?? now()->year,
                'current_semester' => $validated['current_semester'],
                'total_sks' => $validated['total_sks'],
                'gpa' => $validated['gpa'],
            ]);
        } elseif ($validated['role'] === 'dpl') {
            $user->lecturer()->create([
                'study_program_id' => $validated['study_program_id'],
                'nip' => $validated['nip'] ?? null,
                'nidn' => $validated['nidn'] ?? null,
                'position' => $validated['position'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'max_mentee' => $validated['max_mentee'],
            ]);
        } elseif ($validated['role'] === 'kaprodi') {
            $sp = StudyProgram::find($validated['leader_study_program_id']);
            $sp->update(['head_user_id' => $user->id, 'head_name' => $user->name]);
        } elseif ($validated['role'] === 'dekan') {
            $fac = Faculty::find($validated['leader_faculty_id']);
            $fac->update(['dean_user_id' => $user->id, 'dean_name' => $user->name]);
        } elseif ($validated['role'] === 'supervisor-industri') {
            $user->industrySupervisor()->create([
                'industry_id' => $validated['industry_id'],
                'position' => $validated['position'] ?? null,
                'division' => $validated['division'] ?? null,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $user->load('roles', 'student.studyProgram', 'lecturer.studyProgram', 'industrySupervisor.industry');
        
        // Load led faculty or study program if any
        $ledFaculty = Faculty::where('dean_user_id', $user->id)->first();
        $ledStudyProgram = StudyProgram::where('head_user_id', $user->id)->first();

        return view('admin.users.show', compact('user', 'ledFaculty', 'ledStudyProgram'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $faculties = Faculty::where('status', 'active')->get();
        $studyPrograms = StudyProgram::where('status', 'active')->get();
        $industries = Industry::all();

        $user->load('student', 'lecturer', 'industrySupervisor');
        
        $ledFaculty = Faculty::where('dean_user_id', $user->id)->first();
        $ledStudyProgram = StudyProgram::where('head_user_id', $user->id)->first();

        return view('admin.users.edit', compact(
            'user', 'roles', 'faculties', 'studyPrograms', 'industries', 'ledFaculty', 'ledStudyProgram'
        ));
    }

    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'phone' => 'nullable|string|max:20',
        ];

        if ($request->role === 'mahasiswa') {
            $rules['study_program_id'] = 'required|exists:study_programs,id';
            $rules['nim'] = 'required|string|max:30|unique:students,nim,' . ($user->student ? $user->student->id : 'NULL');
            $rules['batch'] = 'nullable|string|max:10';
            $rules['current_semester'] = 'required|integer|min:1';
            $rules['total_sks'] = 'required|integer|min:0';
            $rules['gpa'] = 'required|numeric|min:0|max:4';
        } elseif ($request->role === 'dpl') {
            $rules['study_program_id'] = 'required|exists:study_programs,id';
            $rules['nip'] = 'nullable|string|max:30|unique:lecturers,nip,' . ($user->lecturer ? $user->lecturer->id : 'NULL');
            $rules['nidn'] = 'nullable|string|max:20|unique:lecturers,nidn,' . ($user->lecturer ? $user->lecturer->id : 'NULL');
            $rules['position'] = 'nullable|string|max:255';
            $rules['specialization'] = 'nullable|string|max:255';
            $rules['max_mentee'] = 'required|integer|min:1';
        } elseif ($request->role === 'kaprodi') {
            $rules['leader_study_program_id'] = 'required|exists:study_programs,id';
        } elseif ($request->role === 'dekan') {
            $rules['leader_faculty_id'] = 'required|exists:faculties,id';
        } elseif ($request->role === 'supervisor-industri') {
            $rules['industry_id'] = 'required|exists:industries,id';
            $rules['position'] = 'nullable|string|max:255';
            $rules['division'] = 'nullable|string|max:255';
        }

        $messages = [
            'password.confirmed' => 'Kata sandi dan konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
        ];

        $validated = $request->validate($rules, $messages);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $user->update($userData);
        $user->syncRoles([$validated['role']]);

        // Clean up previous roles
        Faculty::where('dean_user_id', $user->id)->update(['dean_user_id' => null, 'dean_name' => null]);
        StudyProgram::where('head_user_id', $user->id)->update(['head_user_id' => null, 'head_name' => null]);

        if ($validated['role'] !== 'mahasiswa') {
            $user->student()?->delete();
        }
        if ($validated['role'] !== 'dpl') {
            $user->lecturer()?->delete();
        }
        if ($validated['role'] !== 'supervisor-industri') {
            $user->industrySupervisor()?->delete();
        }

        // Create or update new role-specific profile
        if ($validated['role'] === 'mahasiswa') {
            $user->student()->updateOrCreate([], [
                'study_program_id' => $validated['study_program_id'],
                'nim' => $validated['nim'],
                'batch' => $validated['batch'] ?? now()->year,
                'current_semester' => $validated['current_semester'],
                'total_sks' => $validated['total_sks'],
                'gpa' => $validated['gpa'],
            ]);
        } elseif ($validated['role'] === 'dpl') {
            $user->lecturer()->updateOrCreate([], [
                'study_program_id' => $validated['study_program_id'],
                'nip' => $validated['nip'] ?? null,
                'nidn' => $validated['nidn'] ?? null,
                'position' => $validated['position'] ?? null,
                'specialization' => $validated['specialization'] ?? null,
                'max_mentee' => $validated['max_mentee'],
            ]);
        } elseif ($validated['role'] === 'kaprodi') {
            $sp = StudyProgram::find($validated['leader_study_program_id']);
            $sp->update(['head_user_id' => $user->id, 'head_name' => $user->name]);
        } elseif ($validated['role'] === 'dekan') {
            $fac = Faculty::find($validated['leader_faculty_id']);
            $fac->update(['dean_user_id' => $user->id, 'dean_name' => $user->name]);
        } elseif ($validated['role'] === 'supervisor-industri') {
            $user->industrySupervisor()->updateOrCreate([], [
                'industry_id' => $validated['industry_id'],
                'position' => $validated['position'] ?? null,
                'division' => $validated['division'] ?? null,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus(User $user)
    {
        $user->update(['status' => $user->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Status user berhasil diubah.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user->syncRoles([$request->role]);
        return back()->with('success', 'Role berhasil diubah.');
    }

    public function resetPassword(User $user)
    {
        $user->update(['password' => 'password']);
        return back()->with('success', 'Password direset ke "password".');
    }
}
