@extends('layouts.app')

@section('title', 'Seminar / Sidang Ujian Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Seminar / Sidang Ujian Magang</h4>
            <p class="mb-0">Pendaftaran dan informasi pelaksanaan seminar evaluasi hasil magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Sidang Magang</a></li>
        </ol>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- LEFT: STATUS & SCHEDULE CARD -->
    <div class="col-xl-8 col-lg-7">
        @if(!$defense)
            <!-- FORM PENDAFTARAN SIDANG -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-0 pb-0">
                    <h4 class="card-title">Pendaftaran Seminar / Sidang Ujian Magang</h4>
                </div>
                <div class="card-body">
                    <p class="text-muted">Sebelum mendaftar, pastikan Anda telah menyelesaikan magang dan mengunggah draft Laporan Akhir Magang.</p>

                    <form action="{{ route('student.defense.register') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">1. File Slide Presentasi Sidang (.pdf / .ppt / .pptx) <span class="text-danger">*</span></label>
                            <input type="file" name="presentation_file" class="form-control" accept=".pdf,.ppt,.pptx" required>
                            <small class="text-muted">Slide materi presentasi seminar magang (Maks. 10 MB).</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">2. Surat Bebas Tanggungan / Bebas Administrasi (Opsional)</label>
                            <input type="file" name="clearance_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Bukti bebas tanggungan perpustakaan atau administrasi keuangan jika disyaratkan (Maks. 5 MB).</small>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-paper-plane me-1"></i> Ajukan Pendaftaran Sidang
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- JADWAL & STATUS SIDANG -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <span class="badge {{ $defense->status_badge['class'] }} mb-2">
                            {{ $defense->status_badge['label'] }}
                        </span>
                        <h4 class="card-title text-dark fw-bold mb-1">Informasi Seminar Sidang Magang</h4>
                    </div>
                    @if($defense->official_report_number)
                        <small class="text-muted">No. BA: <strong>{{ $defense->official_report_number }}</strong></small>
                    @endif
                </div>

                <div class="card-body">
                    @if($defense->status === 'registered')
                        <div class="alert alert-warning border-0 d-flex align-items-center mb-4">
                            <i class="la la-hourglass-half me-3" style="font-size: 28px;"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Pendaftaran Berhasil Diterima</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Koordinator Program Studi sedang memproses penentuan jadwal sidang dan penunjukan Dosen Penguji.</p>
                            </div>
                        </div>
                    @elseif($defense->status === 'scheduled')
                        <div class="alert alert-info border-0 d-flex align-items-center mb-4">
                            <i class="la la-calendar-check me-3 text-info" style="font-size: 28px;"></i>
                            <div>
                                <h6 class="mb-0 fw-bold">Jadwal Sidang Telah Ditetapkan!</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Harap hadir 15 menit sebelum waktu pelaksanaan dan siapkan materi presentasi Anda.</p>
                            </div>
                        </div>
                    @elseif($defense->status === 'passed')
                        <div class="alert alert-success border-0 d-flex align-items-center mb-4">
                            <i class="la la-trophy me-3 text-success" style="font-size: 32px;"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-success">Selamat! Anda Dinyatakan LULUS Sidang Magang</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Nilai Akhir: <strong>{{ $defense->final_score }} ({{ $defense->grade_letter }})</strong></p>
                            </div>
                        </div>
                    @elseif($defense->status === 'revision')
                        <div class="alert alert-danger border-0 d-flex align-items-center mb-4">
                            <i class="la la-exclamation-triangle me-3 text-danger" style="font-size: 28px;"></i>
                            <div>
                                <h6 class="mb-0 fw-bold text-danger">Perlu Revisi Laporan Hasil Sidang</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Batas waktu revisi: <strong>{{ $defense->revision_deadline?->format('d M Y') ?? '7 hari' }}</strong></p>
                            </div>
                        </div>
                    @endif

                    <!-- DETAIL JADWAL -->
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block"><i class="la la-calendar"></i> Tanggal Pelaksanaan:</small>
                                <strong class="text-dark">{{ $defense->scheduled_date ? $defense->scheduled_date->format('l, d F Y') : 'Menunggu Jadwal' }}</strong>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block"><i class="la la-clock"></i> Waktu Pelaksanaan:</small>
                                <strong class="text-dark">{{ $defense->start_time ? substr($defense->start_time, 0, 5) . ' - ' . substr($defense->end_time, 0, 5) . ' WIB' : '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block"><i class="la la-map-marker"></i> Ruangan / Link Sidang:</small>
                                @if($defense->room_or_link && Str::startsWith($defense->room_or_link, 'http'))
                                    <a href="{{ $defense->room_or_link }}" target="_blank" class="text-primary fw-bold text-break">
                                        <i class="la la-external-link-alt"></i> {{ $defense->room_or_link }}
                                    </a>
                                @else
                                    <strong class="text-dark">{{ $defense->room_or_link ?? 'Belum ditentukan' }}</strong>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- DEWAN PENGUJI -->
                    <h6 class="text-primary fw-bold mb-3">Dewan Penguji & Pembimbing:</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <small class="badge light badge-primary mb-1">Dosen Penguji</small>
                                <h6 class="mb-0 text-dark fw-bold">{{ $defense->examiner?->user?->name ?? 'Belum Ditunjuk' }}</h6>
                                <small class="text-muted">NIDN: {{ $defense->examiner?->nidn ?? '-' }}</small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="border p-3 rounded">
                                <small class="badge light badge-info mb-1">Dosen Pembimbing (DPL)</small>
                                <h6 class="mb-0 text-dark fw-bold">{{ $defense->supervisor?->user?->name ?? 'Belum Ditunjuk' }}</h6>
                                <small class="text-muted">NIDN: {{ $defense->supervisor?->nidn ?? '-' }}</small>
                            </div>
                        </div>
                    </div>

                    @if($defense->revision_notes)
                        <div class="p-3 bg-light rounded mb-4">
                            <h6 class="text-danger fw-bold mb-1"><i class="la la-edit"></i> Catatan Revisi dari Penguji:</h6>
                            <p class="mb-0 text-dark" style="white-space: pre-line;">{{ $defense->revision_notes }}</p>
                        </div>
                    @endif

                    @if($defense->status === 'passed')
                        <div class="border-top pt-3 text-end">
                            <a href="{{ route('dpl.defenses.beritaAcara', $defense->id) }}" target="_blank" class="btn btn-outline-success">
                                <i class="la la-print me-1"></i> Cetak Berita Acara Ujian (PDF)
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- RIGHT: INTERNSHIP SUMMARY -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Ringkasan Program Magang</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Perusahaan Mitra:</small>
                        <strong class="text-dark">{{ $internship->vacancy->industry->name }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Posisi Magang:</small>
                        <span class="text-dark">{{ $internship->vacancy->title }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Dosen Pembimbing:</small>
                        <span class="text-dark">{{ $internship->getDpl()?->user?->name ?? 'Belum diplot' }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Laporan Akhir:</small>
                        @if($finalReport)
                            <span class="badge {{ $finalReport->status_badge['class'] }}">{{ $finalReport->status_badge['label'] }}</span>
                        @else
                            <span class="badge badge-danger">Belum Diunggah</span>
                        @endif
                    </li>
                </ul>

                @if($defense && $defense->presentation_file_path)
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted d-block mb-1">Slide Terunggah:</small>
                        <a href="{{ asset('storage/' . $defense->presentation_file_path) }}" target="_blank" class="btn btn-light btn-sm w-100">
                            <i class="la la-file-powerpoint me-1"></i> Unduh File Slide
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
