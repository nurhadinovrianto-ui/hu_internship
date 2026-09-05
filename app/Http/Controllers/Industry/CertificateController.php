<?php

namespace App\Http\Controllers\Industry;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\Certificate;
use App\Models\IndustryCertificateTemplate;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    private function getSupervisor()
    {
        return auth()->user()->industrySupervisor;
    }

    public function index(Request $request)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor) {
            abort(403, 'Akses ditolak.');
        }

        $query = Internship::with(['student.user', 'student.studyProgram', 'vacancy', 'certificate', 'assessments'])
            ->whereHas('vacancy', fn($q) => $q->where('industry_supervisor_id', $supervisor->id)->orWhere('industry_id', $supervisor->industry_id))
            ->whereIn('status', [Internship::STATUS_ACTIVE, Internship::STATUS_COMPLETED]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('nim', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('cert_status')) {
            if ($request->cert_status === 'issued') {
                $query->has('certificate');
            } elseif ($request->cert_status === 'pending') {
                $query->doesntHave('certificate');
            }
        }

        $internships = $query->latest()->paginate(10)->withQueryString();
        $template = IndustryCertificateTemplate::where('industry_id', $supervisor->industry_id)->first();

        return view('industry.certificate.index', compact('internships', 'template'));
    }

    public function template()
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor || !$supervisor->industry_id) {
            return back()->with('error', 'Profil industri Anda belum terhubung.');
        }

        $template = IndustryCertificateTemplate::firstOrCreate(
            ['industry_id' => $supervisor->industry_id],
            [
                'signatory_name' => $supervisor->user->name,
                'signatory_position' => 'Pembimbing Industri / HRD',
            ]
        );

        return view('industry.certificate.template', compact('template'));
    }

    public function updateTemplate(Request $request)
    {
        $supervisor = $this->getSupervisor();
        if (!$supervisor || !$supervisor->industry_id) {
            abort(403);
        }

        $validated = $request->validate([
            'signatory_name' => 'required|string|max:255',
            'signatory_position' => 'required|string|max:255',
            'background_image' => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
            'seal_image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $template = IndustryCertificateTemplate::firstOrCreate(['industry_id' => $supervisor->industry_id]);

        if ($request->hasFile('background_image')) {
            if ($template->background_image) {
                Storage::disk('public')->delete($template->background_image);
            }
            $bgPath = $request->file('background_image')->store('certificates/templates', 'public');
            $validated['background_image'] = $bgPath;
        }

        if ($request->hasFile('seal_image')) {
            if ($template->seal_image) {
                Storage::disk('public')->delete($template->seal_image);
            }
            $sealPath = $request->file('seal_image')->store('certificates/templates', 'public');
            $validated['seal_image'] = $sealPath;
        }

        $template->update($validated);

        return back()->with('success', 'Desain template sertifikat perusahaan berhasil diperbarui.');
    }

    public function generate(Internship $internship)
    {
        $supervisor = $this->getSupervisor();
        $isAllowed = $supervisor && (
            $internship->vacancy?->industry_supervisor_id == $supervisor->id ||
            ($supervisor->industry_id && $internship->vacancy?->industry_id == $supervisor->industry_id)
        );
        if (!$isAllowed) {
            abort(403, 'Akses ditolak.');
        }

        $assess = $internship->assessments()->where('assessor_type', 'industry')->first();
        if (!$assess) {
            return back()->with('error', 'Mahasiswa ini belum memiliki nilai akhir industri. Berikan penilaian terlebih dahulu sebelum generate sertifikat.');
        }

        $student = $internship->student;
        $certificateNumber = Certificate::generateNumber($student->id);

        $certificate = Certificate::updateOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $student->id,
                'certificate_number' => $certificateNumber,
                'issuance_type' => 'auto_generated',
                'file_path' => null,
                'issued_at' => now(),
                'issued_by' => auth()->id(),
            ]
        );

        return back()->with('success', "Sertifikat untuk {$student->user->name} berhasil digenerate dengan nomor {$certificate->certificate_number}.");
    }

    public function uploadManual(Request $request, Internship $internship)
    {
        $supervisor = $this->getSupervisor();
        $isAllowed = $supervisor && (
            $internship->vacancy?->industry_supervisor_id == $supervisor->id ||
            ($supervisor->industry_id && $internship->vacancy?->industry_id == $supervisor->industry_id)
        );
        if (!$isAllowed) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'certificate_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificate_number' => 'nullable|string|max:255',
        ]);

        $student = $internship->student;
        $file = $request->file('certificate_file');
        $path = $file->storeAs(
            'certificates/manual',
            "sertifikat_{$student->nim}_" . time() . "." . $file->getClientOriginalExtension(),
            'public'
        );

        $certNum = $request->certificate_number ?: Certificate::generateNumber($student->id);

        $certificate = Certificate::updateOrCreate(
            ['internship_id' => $internship->id],
            [
                'student_id' => $student->id,
                'certificate_number' => $certNum,
                'file_path' => $path,
                'issuance_type' => 'manual_upload',
                'issued_at' => now(),
                'issued_by' => auth()->id(),
            ]
        );

        return back()->with('success', "File sertifikat manual untuk {$student->user->name} berhasil diunggah.");
    }

    public function download(Internship $internship)
    {
        $supervisor = $this->getSupervisor();
        $isAllowed = $supervisor && (
            $internship->vacancy?->industry_supervisor_id == $supervisor->id ||
            ($supervisor->industry_id && $internship->vacancy?->industry_id == $supervisor->industry_id)
        );
        if (!$isAllowed) {
            abort(403, 'Akses ditolak.');
        }

        $certificate = $internship->certificate;
        if (!$certificate) {
            return back()->with('error', 'Sertifikat belum tersedia.');
        }

        if ($certificate->issuance_type === 'manual_upload' && $certificate->file_path && Storage::disk('public')->exists($certificate->file_path)) {
            return Storage::disk('public')->download($certificate->file_path);
        }

        $student = $internship->student;
        $assess = $internship->assessments()->where('assessor_type', 'industry')->first();
        $template = IndustryCertificateTemplate::where('industry_id', $supervisor->industry_id)->first();

        $verifyUrl = url('/verify-certificate/' . $certificate->certificate_number);
        $qrCode = base64_encode(QrCode::format('svg')->size(140)->generate($verifyUrl));

        $data = [
            'student' => $student,
            'internship' => $internship,
            'certificate' => $certificate,
            'industryScore' => $assess?->final_score ?? '-',
            'template' => $template,
            'qrCode' => $qrCode,
        ];

        $safeNim = str_replace(['/', '\\'], '-', $student->nim);
        $pdf = Pdf::loadView('industry.certificate.pdf', $data)->setPaper('a4', 'landscape');
        return $pdf->download("Sertifikat_Industri_{$safeNim}.pdf");
    }
}
