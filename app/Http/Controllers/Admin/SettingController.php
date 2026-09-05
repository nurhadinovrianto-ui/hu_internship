<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings.app_name' => 'required|string|max:255',
            'settings.app_short_name' => 'required|string|max:50',
            'settings.min_sks' => 'required|integer|min:0|max:250',
            'settings.min_gpa' => 'required|numeric|min:0|max:4',
            'settings.min_days_vacancy_deadline' => 'required|integer|min:0',
            'settings.contact_email' => 'required|email|max:255',
            'settings.contact_phone' => 'required|string|max:50',
            'settings.system_version' => 'required|string|max:20',
            'settings.google_client_id' => 'nullable|string|max:255',
            'settings.google_client_secret' => 'nullable|string|max:255',
            'settings.google_redirect_uri' => 'nullable|url|max:255',
            'settings.jitsi_domain' => 'required|string|max:255',
            'settings.max_active_applications' => 'required|integer|min:1',
            'settings.geofence_radius_meters' => 'required|integer|min:0',
            'settings.max_cv_size_kb' => 'required|integer|min:100',
            'settings.max_logbook_size_kb' => 'required|integer|min:100',
            'settings.max_report_size_kb' => 'required|integer|min:100',
            'settings.use_campus_geofencing' => 'required|in:0,1',
            'settings.use_industry_geofencing' => 'required|in:0,1',
            'settings.campus_latitude' => 'required|numeric',
            'settings.campus_longitude' => 'required|numeric',
            'settings.grade_weight_industry' => 'required|integer|min:0|max:100',
            'settings.grade_weight_dpl' => 'required|integer|min:0|max:100',
        ]);

        $request->validate([
            'settings_files.*' => 'nullable|image|mimes:jpeg,png,jpg,svg,ico|max:2048',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::setValue($key, $value);
        }

        if ($request->hasFile('settings_files')) {
            foreach ($request->file('settings_files') as $key => $file) {
                $path = $file->store('settings', 'public');
                Setting::setValue($key, $path);
            }
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan aplikasi berhasil diperbarui.');
    }
}
