<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\IndustrySupervisor;
use App\Models\IndustryCertificateTemplate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class IndustryController extends Controller
{
    public function index(Request $request)
    {
        $query = Industry::withCount('vacancies')->with('supervisors.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('industry_type', 'like', "%{$search}%");
            });
        }

        if ($request->filled('partnership_status')) {
            $query->where('partnership_status', $request->partnership_status);
        }

        if ($request->filled('is_partner')) {
            $query->where('is_active', $request->is_partner);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $industries = $query->latest()->paginate(20)->withQueryString();
        return view('admin.industries.index', compact('industries'));
    }

    public function create() { return view('admin.industries.create'); }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry_type' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'province' => 'nullable|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'contact_person' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'website' => 'nullable|url',
            'partnership_status' => 'required|in:mou,moa,none',
            'mou_start_date' => 'nullable|date',
            'mou_end_date' => 'nullable|date|after_or_equal:mou_start_date',
            'supervisor_name' => 'nullable|string|max:255',
            'supervisor_email' => 'nullable|email|max:255|unique:users,email',
            'supervisor_password' => 'nullable|string|min:6',
            'supervisor_position' => 'nullable|string|max:100',
            'supervisor_division' => 'nullable|string|max:100',
        ], [
            'supervisor_email.unique' => 'Email login supervisor sudah digunakan oleh akun lain.',
        ]);

        $baseSlug = Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        while (Industry::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $validated['slug'] = $slug;

        $supervisorName = trim($request->supervisor_name ?: ($request->contact_person ?: 'PIC ' . $validated['name']));
        $plainPassword = $request->supervisor_password ?: 'password123';

        $supervisorEmail = $request->supervisor_email;
        if (!$supervisorEmail) {
            $baseEmail = strtolower($validated['email']);
            if (User::where('email', $baseEmail)->exists()) {
                $emailParts = explode('@', $baseEmail);
                $namePart = $emailParts[0] ?? 'supervisor';
                $domainPart = $emailParts[1] ?? 'mitra.com';
                $candidate = "{$namePart}.pic@{$domainPart}";
                $c = 1;
                while (User::where('email', $candidate)->exists()) {
                    $candidate = "{$namePart}.pic{$c}@{$domainPart}";
                    $c++;
                }
                $supervisorEmail = $candidate;
            } else {
                $supervisorEmail = $baseEmail;
            }
        }

        $industry = null;
        $user = null;

        DB::transaction(function () use ($validated, $request, $supervisorName, $supervisorEmail, $plainPassword, &$industry, &$user) {
            $industry = Industry::create($validated);

            $user = User::create([
                'name' => $supervisorName,
                'email' => $supervisorEmail,
                'password' => $plainPassword,
                'phone' => $validated['phone'] ?? null,
                'status' => 'active',
            ]);
            $user->assignRole('supervisor-industri');

            $user->industrySupervisor()->create([
                'industry_id' => $industry->id,
                'position' => $request->supervisor_position ?: 'Pembimbing Lapangan / HR',
                'division' => $request->supervisor_division ?: 'Human Resources',
                'is_primary' => true,
            ]);

            IndustryCertificateTemplate::firstOrCreate(
                ['industry_id' => $industry->id],
                [
                    'signatory_name' => $supervisorName,
                    'signatory_position' => $request->supervisor_position ?: 'Pimpinan / HR Manager',
                ]
            );
        });

        return redirect()->route('admin.industries.index')->with(
            'success',
            "Mitra {$industry->name} berhasil ditambahkan! Akun login supervisor otomatis dibuat (Email: {$supervisorEmail} | Password: {$plainPassword})."
        );
    }

    public function show(Industry $industry)
    {
        $industry->load('supervisors.user', 'vacancies.applications');
        return view('admin.industries.show', compact('industry'));
    }

    public function edit(Industry $industry) { return view('admin.industries.edit', compact('industry')); }

    public function update(Request $request, Industry $industry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'industry_type' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'partnership_status' => 'required|in:mou,moa,none',
        ]);
        $industry->update($validated);
        return redirect()->route('admin.industries.index')->with('success', 'Data industri berhasil diupdate.');
    }

    public function destroy(Industry $industry)
    {
        if ($industry->vacancies()->count() > 0) {
            return redirect()->route('admin.industries.index')->with('error', 'Industri tidak dapat dihapus karena masih memiliki data lowongan atau pelamar terkait.');
        }

        DB::transaction(function () use ($industry) {
            foreach ($industry->supervisors as $supervisor) {
                $user = $supervisor->user;
                $supervisor->delete();
                if ($user && $user->hasRole('supervisor-industri') && !$user->hasRole('super-admin')) {
                    $user->delete();
                }
            }
            $industry->delete();
        });

        return redirect()->route('admin.industries.index')->with('success', 'Industri dan akun supervisor terkait berhasil dihapus.');
    }

    public function togglePartner(Industry $industry)
    {
        $industry->update(['is_active' => !$industry->is_active]);
        return back()->with('success', 'Status mitra berhasil diubah.');
    }
}
