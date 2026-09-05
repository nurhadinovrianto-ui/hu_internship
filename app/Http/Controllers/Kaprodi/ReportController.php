<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\FinalReport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = FinalReport::with(['student.user', 'internship.vacancy.industry', 'reviewer'])
            ->where('report_type', 'dpl')
            ->whereIn('status', ['dpl_approved', 'kaprodi_received']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reports = $query->latest('dpl_approved_at')->paginate(15);

        return view('kaprodi.reports.index', compact('reports'));
    }

    public function receive(Request $request, FinalReport $report)
    {
        abort_unless($report->status === 'dpl_approved', 403, 'Laporan belum disetujui DPL.');

        $report->update([
            'status' => 'kaprodi_received',
            'kaprodi_submitted_at' => now(),
        ]);

        return redirect()->route('kaprodi.reports.index')->with('success', 'Laporan akhir berhasil diterima.');
    }
}
