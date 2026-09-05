<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\UsersImport;
use App\Exports\Templates\StudentTemplateExport;
use App\Exports\Templates\LecturerTemplateExport;
use App\Exports\Templates\IndustrySupervisorTemplateExport;
use App\Exports\Templates\StaffTemplateExport;
use Exception;

class UserImportController extends Controller
{
    public function downloadTemplate($role)
    {
        $fileName = 'Template_Import_' . ucfirst($role) . '.xlsx';
        
        return match ($role) {
            'mahasiswa' => Excel::download(new StudentTemplateExport, $fileName),
            'dpl', 'kaprodi', 'dekan' => Excel::download(new LecturerTemplateExport, $fileName),
            'supervisor-industri' => Excel::download(new IndustrySupervisorTemplateExport, $fileName),
            'staff' => Excel::download(new StaffTemplateExport, 'Template_Import_Staff.xlsx'),
            default => redirect()->back()->with('error', 'Template untuk role tersebut tidak ditemukan.'),
        };
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
            'role' => 'required|string',
        ]);

        try {
            Excel::import(new UsersImport($request->role), $request->file('file'));
            return redirect()->back()->with('success', 'Data pengguna berhasil diimpor!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }
    }
}
