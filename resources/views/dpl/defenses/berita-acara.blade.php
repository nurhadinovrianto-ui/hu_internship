<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Seminar Magang - {{ $defense->student->user->name }}</title>
    <link rel="stylesheet" href="{{ asset('edumin/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        body {
            background: #f8fafc;
            font-family: 'Times New Roman', Times, serif;
            color: #111;
        }
        .page-document {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 20mm 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .kop-surat {
            border-bottom: 3px double #000;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .table-doc td, .table-doc th {
            padding: 6px 8px;
            font-size: 14px;
        }
        @media print {
            body { background: #fff; }
            .page-document {
                box-shadow: none;
                margin: 0;
                padding: 10mm 15mm;
                width: 100%;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3 bg-dark">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4">
        <i class="la la-print me-1"></i> Cetak Dokumen (PDF)
    </button>
    <button onclick="window.close()" class="btn btn-secondary btn-sm px-3 ms-2">
        Tutup
    </button>
</div>

<div class="page-document">
    <!-- KOP SURAT UNIVERSITAS -->
    <div class="kop-surat d-flex align-items-center">
        <img src="{{ asset('edumin/images/logo-text.png') }}" style="height: 65px; margin-right: 20px;" alt="Logo Universitas" onerror="this.style.display='none'">
        <div class="text-center flex-grow-1">
            <h4 class="mb-0 fw-bold" style="letter-spacing: 1px;">UNIVERSITAS HORIZON INDONESIA</h4>
            <h5 class="mb-1 fw-bold">{{ strtoupper($defense->student->studyProgram?->faculty?->name ?? 'FAKULTAS TEKNOLOGI INFORMASI') }}</h5>
            <p class="mb-0" style="font-size: 13px;">Jl. Raya Galuh Mas Blok C No. 1, Telukjambe Timur, Karawang - Jawa Barat 41361</p>
            <p class="mb-0" style="font-size: 12px;">Website: www.horizon.ac.id | Email: info@horizon.ac.id</p>
        </div>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div class="text-center my-4">
        <h5 class="fw-bold mb-1" style="text-decoration: underline;">BERITA ACARA SEMINAR HASIL MAGANG</h5>
        <p class="mb-0" style="font-size: 13px;">Nomor: {{ $defense->official_report_number ?? 'BA-MAGANG/'.date('Y').'/001' }}</p>
    </div>

    <!-- ISI BERITA ACARA -->
    <p style="font-size: 14px; text-align: justify; line-height: 1.6;">
        Pada hari ini, <strong>{{ $defense->scheduled_date ? $defense->scheduled_date->translatedFormat('l') : '...' }}</strong> 
        tanggal <strong>{{ $defense->scheduled_date ? $defense->scheduled_date->format('d F Y') : '...' }}</strong>, 
        telah dilaksanakan Seminar Hasil Program Magang / Praktik Kerja Lapangan (PKL) mahasiswa Program Studi 
        <strong>{{ $defense->student->studyProgram?->name }}</strong> Universitas Horizon Indonesia:
    </p>

    <!-- IDENTITAS MAHASISWA -->
    <table class="table table-bordered table-doc mb-4">
        <tbody>
            <tr>
                <td style="width: 25%;"><strong>Nama Mahasiswa</strong></td>
                <td>{{ $defense->student->user->name }}</td>
            </tr>
            <tr>
                <td><strong>Nomor Induk Mahasiswa (NIM)</strong></td>
                <td>{{ $defense->student->nim }}</td>
            </tr>
            <tr>
                <td><strong>Program Studi / Fakultas</strong></td>
                <td>{{ $defense->student->studyProgram?->name }} / {{ $defense->student->studyProgram?->faculty?->name }}</td>
            </tr>
            <tr>
                <td><strong>Tempat / Mitra Magang</strong></td>
                <td>{{ $defense->internship->vacancy->industry->name }}</td>
            </tr>
            <tr>
                <td><strong>Posisi Magang</strong></td>
                <td>{{ $defense->internship->vacancy->title }}</td>
            </tr>
            <tr>
                <td><strong>Waktu & Tempat Sidang</strong></td>
                <td>{{ $defense->scheduled_date ? $defense->scheduled_date->format('d M Y') : '-' }}, Jam {{ substr($defense->start_time, 0, 5) }} WIB ({{ $defense->room_or_link }})</td>
            </tr>
        </tbody>
    </table>

    <!-- HASIL EVALUASI -->
    <p style="font-size: 14px;" class="fw-bold mb-2">Berdasarkan hasil evaluasi dan pengujian dewan sidang, ditetapkan nilai akhir sebagai berikut:</p>
    <table class="table table-bordered table-doc text-center mb-4">
        <thead class="bg-light">
            <tr>
                <th>No</th>
                <th>Nama Penilai</th>
                <th>Peran</th>
                <th>Nilai (0-100)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($defense->scores as $index => $score)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-start">{{ $score->evaluator->name }}</td>
                    <td>{{ $score->evaluator_role === 'examiner' ? 'Dosen Penguji' : 'Dosen Pembimbing (DPL)' }}</td>
                    <td><strong>{{ number_format($score->average_score, 2) }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted">Belum ada rincian nilai</td>
                </tr>
            @endforelse
            <tr class="fw-bold bg-light">
                <td colspan="3" class="text-end">Nilai Rata-Rata Akhir Sidang:</td>
                <td><h6 class="mb-0 fw-bold">{{ $defense->final_score ? number_format($defense->final_score, 2) : '-' }} ({{ $defense->grade_letter ?? '-' }})</h6></td>
            </tr>
            <tr class="fw-bold">
                <td colspan="3" class="text-end">Status Kelulusan:</td>
                <td><span class="badge {{ $defense->status === 'passed' ? 'bg-success' : 'bg-warning' }} text-white">{{ $defense->status_badge['label'] }}</span></td>
            </tr>
        </tbody>
    </table>

    @if($defense->revision_notes)
        <div class="mb-4" style="font-size: 13px;">
            <strong>Catatan Revisi Penguji:</strong>
            <p class="mb-0 text-muted fst-italic">{{ $defense->revision_notes }}</p>
            <small class="text-danger">Batas waktu penyerahan revisi: {{ $defense->revision_deadline?->format('d F Y') ?? '7 hari kerja' }}</small>
        </div>
    @endif

    <p style="font-size: 14px; text-align: justify; line-height: 1.6;">
        Demikian Berita Acara ini dibuat dengan sebenarnya untuk dipergunakan sebagai kelengkapan penilaian kelulusan program magang akademik.
    </p>

    <!-- TANDA TANGAN -->
    <div class="row mt-5 pt-3 text-center" style="font-size: 14px;">
        <div class="col-6">
            <p class="mb-1">Dosen Pembimbing (DPL),</p>
            <div class="my-4">
                <i class="la la-check-circle text-success" style="font-size: 38px;"></i>
                <br><small class="text-muted" style="font-size: 10px;">Digital Signature Verified</small>
            </div>
            <strong><u>{{ $defense->supervisor?->user?->name ?? 'DPL Magang' }}</u></strong>
            <p class="mb-0" style="font-size: 12px;">NIDN: {{ $defense->supervisor?->nidn ?? '-' }}</p>
        </div>
        <div class="col-6">
            <p class="mb-1">Dosen Penguji,</p>
            <div class="my-4">
                <i class="la la-check-circle text-success" style="font-size: 38px;"></i>
                <br><small class="text-muted" style="font-size: 10px;">Digital Signature Verified</small>
            </div>
            <strong><u>{{ $defense->examiner?->user?->name ?? 'Dosen Penguji' }}</u></strong>
            <p class="mb-0" style="font-size: 12px;">NIDN: {{ $defense->examiner?->nidn ?? '-' }}</p>
        </div>
    </div>
</div>

</body>
</html>
