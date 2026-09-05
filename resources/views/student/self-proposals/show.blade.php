@extends('layouts.app')

@section('title', 'Rincian Usulan Magang Mandiri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Rincian Usulan Magang Mandiri</h4>
            <p class="mb-0">Informasi dan status persetujuan berjenjang DPL & Kaprodi.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.self-proposals.index') }}">Magang Mandiri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Rincian</a></li>
        </ol>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="la la-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Stepper Progres Usulan Magang Mandiri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body py-4">
                <div class="row text-center position-relative">
                    <!-- Step 1 -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-2" style="width: 48px; height: 48px;">
                            <i class="la la-check fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-1">1. Pengajuan Usulan</h6>
                        <small class="text-muted d-block">{{ $proposal->created_at->format('d M Y, H:i') }}</small>
                        <span class="badge badge-sm badge-success">Selesai Diajukan</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="col-md-4 mb-3 mb-md-0">
                        @if($proposal->dpl_status === 'approved')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-check fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">2. Review Dosen DPL</h6>
                            <small class="text-muted d-block">{{ $proposal->dpl_reviewed_at ? $proposal->dpl_reviewed_at->format('d M Y, H:i') : '-' }}</small>
                            <span class="badge badge-sm badge-success">Disetujui DPL</span>
                        @elseif($proposal->dpl_status === 'revision')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-pencil-alt fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">2. Review Dosen DPL</h6>
                            <small class="text-muted d-block">Perlu Tindakan</small>
                            <span class="badge badge-sm badge-warning">Perlu Revisi</span>
                        @elseif($proposal->dpl_status === 'rejected')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-times fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">2. Review Dosen DPL</h6>
                            <small class="text-muted d-block">Tidak Disetujui</small>
                            <span class="badge badge-sm badge-danger">Ditolak DPL</span>
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-info text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-clock fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">2. Review Dosen DPL</h6>
                            <small class="text-muted d-block">Sedang Ditinjau</small>
                            <span class="badge badge-sm badge-info">Menunggu Review DPL</span>
                        @endif
                    </div>

                    <!-- Step 3 -->
                    <div class="col-md-4">
                        @if($proposal->status === 'approved')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-check-double fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">3. Persetujuan Final Kaprodi</h6>
                            <small class="text-muted d-block">{{ $proposal->reviewed_at ? $proposal->reviewed_at->format('d M Y, H:i') : '-' }}</small>
                            <span class="badge badge-sm badge-success">Disetujui Resmi</span>
                        @elseif($proposal->status === 'revision')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-exclamation-triangle fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">3. Persetujuan Final Kaprodi</h6>
                            <small class="text-muted d-block">Perlu Revisi</small>
                            <span class="badge badge-sm badge-warning">Catatan Kaprodi</span>
                        @elseif($proposal->status === 'rejected')
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-times-circle fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">3. Persetujuan Final Kaprodi</h6>
                            <small class="text-muted d-block">Tidak Disetujui</small>
                            <span class="badge badge-sm badge-danger">Ditolak Kaprodi</span>
                        @else
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-secondary text-white mb-2" style="width: 48px; height: 48px;">
                                <i class="la la-hourglass-half fs-4"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">3. Persetujuan Final Kaprodi</h6>
                            <small class="text-muted d-block">Menunggu Tahap 2</small>
                            <span class="badge badge-sm badge-secondary">Menunggu Validasi Akhir</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Rincian Usulan & Catatan Review -->
    <div class="col-xl-8 col-lg-7">
        <!-- Alert Revisi Jika Diperlukan -->
        @if(in_array($proposal->status, ['submitted', 'revision']) && ($proposal->dpl_status === 'revision' || $proposal->status === 'revision'))
            <div class="alert alert-warning mb-4 shadow-sm border-0 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-warning-emphasis"><i class="la la-exclamation-triangle me-1"></i> Usulan Magang Mandiri Memerlukan Perbaikan!</h6>
                    <p class="mb-0 text-dark">Silakan periksa catatan revisi di bawah dan lakukan pembaruan data usulan atau unggah dokumen yang sesuai.</p>
                </div>
                <a href="{{ route('student.self-proposals.edit', $proposal->id) }}" class="btn btn-warning btn-sm text-white">
                    <i class="la la-pencil-alt me-1"></i> Perbaiki Sekarang
                </a>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="badge {{ $proposal->status_badge['class'] }} mb-2">
                        {{ $proposal->status_badge['label'] }}
                    </span>
                    <h4 class="card-title text-dark fw-bold mb-1">{{ $proposal->company_name }}</h4>
                    <p class="text-muted mb-0"><i class="la la-briefcase text-primary"></i> Posisi: <strong>{{ $proposal->position_title }}</strong></p>
                </div>
                <small class="text-muted">Diajukan: {{ $proposal->created_at->format('d M Y, H:i') }}</small>
            </div>

            <div class="card-body">
                <!-- Catatan DPL -->
                @if($proposal->dpl_notes)
                    <div class="alert alert-{{ $proposal->dpl_status === 'approved' ? 'primary' : ($proposal->dpl_status === 'revision' ? 'warning' : 'danger') }} mb-3 border-0">
                        <h6 class="fw-bold mb-1">
                            <i class="la la-user-tie me-1"></i> Catatan Evaluasi DPL ({{ $proposal->dpl?->user->name ?? 'Dosen Pembimbing' }}):
                        </h6>
                        <p class="mb-0">{{ $proposal->dpl_notes }}</p>
                    </div>
                @endif

                <!-- Catatan Kaprodi -->
                @if($proposal->kaprodi_notes)
                    <div class="alert alert-{{ $proposal->status === 'approved' ? 'success' : ($proposal->status === 'revision' ? 'warning' : 'danger') }} mb-4 border-0">
                        <h6 class="fw-bold mb-1">
                            <i class="la la-university me-1"></i> Catatan Koordinator Program Studi:
                        </h6>
                        <p class="mb-0">{{ $proposal->kaprodi_notes }}</p>
                    </div>
                @endif

                <h6 class="text-primary fw-bold mb-2">Deskripsi & Ruang Lingkup Tugas:</h6>
                <div class="p-3 bg-light rounded mb-4 text-dark" style="white-space: pre-line;">{{ $proposal->job_description }}</div>

                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Bidang Usaha:</small>
                        <span class="text-dark fw-medium">{{ $proposal->industry_sector ?? '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Periode Magang:</small>
                        <span class="text-dark fw-medium">{{ $proposal->start_date->format('d M Y') }} s/d {{ $proposal->end_date->format('d M Y') }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Alamat Kantor:</small>
                        <span class="text-dark fw-medium">{{ $proposal->company_address }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Radius Geofence Presensi:</small>
                        <span class="text-dark fw-medium">{{ $proposal->geofence_radius }} Meter</span>
                    </div>
                </div>

                <div class="border-top pt-3 mt-4">
                    <h6 class="text-primary fw-bold mb-2">Dokumen Penerimaan (Letter of Acceptance):</h6>
                    <a href="{{ asset('storage/' . $proposal->loa_file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="la la-file-pdf me-1"></i> Buka / Unduh Dokumen LoA
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: DPL, Pembimbing Industri, dan Akun Mitra -->
    <div class="col-xl-4 col-lg-5">
        <!-- Card DPL Pembimbing -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">
                    <i class="la la-user-tie text-primary me-1"></i> Dosen Pembimbing (DPL)
                </h5>
            </div>
            <div class="card-body">
                @if($proposal->dpl)
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar avatar-md me-3 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="la la-user-graduate fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-dark fw-bold">{{ $proposal->dpl->user->name }}</h6>
                            <small class="text-muted">NIDN: {{ $proposal->dpl->nidn }}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                        <small class="text-muted">Status Evaluasi DPL:</small>
                        <span class="badge {{ $proposal->dpl_status_badge['class'] }}">
                            {{ $proposal->dpl_status_badge['label'] }}
                        </span>
                    </div>
                @else
                    <p class="text-muted mb-0"><i class="la la-info-circle text-info"></i> Belum ada DPL yang dihubungkan ke usulan ini.</p>
                @endif
            </div>
        </div>

        <!-- Card Akun Pembimbing Industri Otomatis -->
        @if($proposal->status === 'approved' && $proposal->partner_account_created)
            <div class="card shadow-sm border-0 mb-4 border-start border-success border-4">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title text-success">
                        <i class="la la-key me-1"></i> Akun Pembimbing Mitra (Aktif)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-3">
                        Akun resmi Supervisor Industri telah otomatis dibuat. Berikan akses login berikut kepada pembimbing perusahaan Anda agar dapat memantau logbook & presensi magang.
                    </p>
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Login URL:</small>
                            <span class="text-dark fw-bold">{{ route('login') }}</span>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted d-block">Email Login:</small>
                            <span class="text-dark fw-bold">{{ $proposal->contact_person_email }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Password Awal:</small>
                            <div class="input-group input-group-sm">
                                <input type="text" id="partner-temp-pass" class="form-control form-control-sm bg-white" value="{{ $proposal->partner_temp_password ?? 'Hubungi Kaprodi' }}" readonly>
                                <button class="btn btn-primary btn-sm" type="button" onclick="copyPartnerPass()">
                                    <i class="la la-copy"></i> Salin
                                </button>
                            </div>
                        </div>
                    </div>
                    <small class="text-success d-block"><i class="la la-check-circle"></i> Mentor industri dapat langsung masuk ke portal SIMANG.</small>
                </div>
            </div>
        @endif

        <!-- Card Narahubung Perusahaan -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Data Narahubung Mitra</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Nama Lengkap:</small>
                        <strong class="text-dark">{{ $proposal->contact_person_name }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Jabatan:</small>
                        <span class="text-dark">{{ $proposal->contact_person_position ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Email:</small>
                        <span class="text-dark">{{ $proposal->contact_person_email ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">No. Telepon / WA:</small>
                        <span class="text-dark">{{ $proposal->contact_person_phone ?? '-' }}</span>
                    </li>
                </ul>

                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('student.self-proposals.index') }}" class="btn btn-light w-100">
                        <i class="la la-arrow-left me-1"></i> Kembali
                    </a>
                    @if(in_array($proposal->status, ['submitted', 'revision']))
                        <a href="{{ route('student.self-proposals.edit', $proposal->id) }}" class="btn btn-warning text-white w-100">
                            <i class="la la-edit me-1"></i> Edit Usulan
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyPartnerPass() {
        const passInput = document.getElementById('partner-temp-pass');
        if (passInput) {
            passInput.select();
            passInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(passInput.value);
            alert('Password akun mitra berhasil disalin!');
        }
    }
</script>
@endsection
