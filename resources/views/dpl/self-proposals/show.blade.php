@extends('layouts.app')

@section('title', 'Tinjau Usulan Magang Mandiri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Tinjau Usulan Magang Mandiri</h4>
            <p class="mb-0">Evaluasi kelayakan akademik dan relevansi tempat magang mahasiswa bimbingan.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dpl.self-proposals.index') }}">Usulan Magang</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="la la-check-circle me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="la la-exclamation-triangle me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <!-- Kolom Kiri: Detail Usulan & Dokumen -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="badge {{ $proposal->dpl_status_badge['class'] }} mb-2">
                        Status DPL: {{ $proposal->dpl_status_badge['label'] }}
                    </span>
                    <span class="badge {{ $proposal->status_badge['class'] }} mb-2 ms-1">
                        Status Kaprodi: {{ $proposal->status_badge['label'] }}
                    </span>
                    <h4 class="card-title text-dark fw-bold mb-1">{{ $proposal->company_name }}</h4>
                    <p class="text-primary fw-medium mb-0"><i class="la la-briefcase"></i> Posisi: {{ $proposal->position_title }}</p>
                </div>
                <small class="text-muted">Diajukan: {{ $proposal->created_at->format('d M Y, H:i') }}</small>
            </div>

            <div class="card-body">
                <!-- Banner Evaluasi DPL jika sudah ada -->
                @if($proposal->dpl_notes)
                    <div class="alert alert-{{ $proposal->dpl_status === 'approved' ? 'primary' : ($proposal->dpl_status === 'revision' ? 'warning' : 'danger') }} mb-4 border-0">
                        <h6 class="fw-bold mb-1"><i class="la la-comment-alt me-1"></i> Catatan Evaluasi DPL Anda:</h6>
                        <p class="mb-0">{{ $proposal->dpl_notes }}</p>
                        <small class="text-muted d-block mt-1">Dievaluasi pada: {{ $proposal->dpl_reviewed_at?->format('d M Y, H:i') ?? '-' }}</small>
                    </div>
                @endif

                <!-- Banner Catatan Kaprodi jika ada -->
                @if($proposal->kaprodi_notes)
                    <div class="alert alert-secondary mb-4 border-0">
                        <h6 class="fw-bold mb-1"><i class="la la-university me-1"></i> Catatan Koordinator Program Studi:</h6>
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
                    <div class="col-sm-12">
                        <small class="text-muted d-block">Alamat Lokasi Magang:</small>
                        <span class="text-dark fw-medium">{{ $proposal->company_address }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Radius Geofence Presensi:</small>
                        <span class="text-dark fw-medium">{{ $proposal->geofence_radius }} Meter</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Koordinat Titik:</small>
                        <span class="text-dark fw-medium">{{ $proposal->latitude ?? '-' }}, {{ $proposal->longitude ?? '-' }}</span>
                    </div>
                </div>

                <div class="border-top pt-3 mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="text-primary fw-bold mb-1">Bukti Penerimaan (Letter of Acceptance):</h6>
                        <small class="text-muted">Dokumen resmi penerimaan magang dari perusahaan mitra.</small>
                    </div>
                    <a href="{{ asset('storage/' . $proposal->loa_file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="la la-file-pdf me-1"></i> Buka Dokumen LoA
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Mahasiswa, Narahubung Mitra, & Tindakan Evaluasi -->
    <div class="col-xl-4 col-lg-5">
        <!-- Card Data Mahasiswa -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Data Mahasiswa Pengusul</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-lg me-3 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="la la-user-graduate fs-3"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">{{ $proposal->student->user->name }}</h6>
                        <small class="text-muted d-block">NIM: {{ $proposal->student->nim }}</small>
                        <small class="text-primary">{{ $proposal->student->studyProgram->name ?? '-' }}</small>
                    </div>
                </div>
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Email:</span>
                        <strong class="text-dark">{{ $proposal->student->user->email }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">No. Handphone:</span>
                        <strong class="text-dark">{{ $proposal->student->phone ?? '-' }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Card Narahubung Mitra -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Narahubung / Supervisor Mitra</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush small">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Nama PIC / Mentor:</small>
                        <strong class="text-dark">{{ $proposal->contact_person_name }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Jabatan:</small>
                        <span class="text-dark">{{ $proposal->contact_person_position ?? '-' }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Email:</small>
                        <span class="text-dark">{{ $proposal->contact_person_email }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">No. Telepon / WA:</small>
                        <span class="text-dark">{{ $proposal->contact_person_phone ?? '-' }}</span>
                    </li>
                </ul>
                <div class="mt-3 p-2 bg-light rounded text-muted small">
                    <i class="la la-info-circle text-primary"></i> Jika usulan disetujui Kaprodi, sistem akan otomatis membuatkan akun login resmi untuk email di atas.
                </div>
            </div>
        </div>

        <!-- Card Tindakan Evaluasi DPL -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Tindakan Review DPL</h5>
            </div>
            <div class="card-body">
                @if($proposal->dpl_status === 'approved')
                    <div class="alert alert-success py-2 px-3 mb-3">
                        <i class="la la-check-circle me-1"></i> Anda telah menyetujui usulan magang mandiri ini. Menunggu keputusan final Kaprodi.
                    </div>
                @endif

                <div class="d-grid gap-2">
                    <!-- Tombol Setujui -->
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove">
                        <i class="la la-check-circle me-1"></i> {{ $proposal->dpl_status === 'approved' ? 'Perbarui Persetujuan DPL' : 'Setujui Usulan Magang' }}
                    </button>

                    <!-- Tombol Minta Revisi -->
                    <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalRevision">
                        <i class="la la-pencil-alt me-1"></i> Minta Revisi ke Mahasiswa
                    </button>

                    <!-- Tombol Tolak -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject">
                        <i class="la la-times-circle me-1"></i> Tolak Usulan
                    </button>

                    <a href="{{ route('dpl.self-proposals.index') }}" class="btn btn-light mt-2">
                        <i class="la la-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Setujui -->
<div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dpl.self-proposals.approve', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title text-success"><i class="la la-check-circle me-1"></i> Persetujuan DPL Magang Mandiri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">
                        Apakah Anda menyetujui usulan tempat magang mandiri mahasiswa <strong>{{ $proposal->student->user->name }}</strong> di <strong>{{ $proposal->company_name }}</strong>?
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan / Rekomendasi DPL (Opsional):</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Relevansi kompetensi software engineering telah sesuai kurikulum. Tempat magang memenuhi kriteria.">{{ old('notes', $proposal->dpl_notes) }}</textarea>
                    </div>
                    <div class="alert alert-info py-2 px-3 small">
                        <i class="la la-info-circle me-1"></i> Setelah disetujui DPL, usulan akan diteruskan ke Kaprodi untuk validasi institusional dan pembuatan otomatis akun pembimbing mitra.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Ya, Setujui Usulan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Minta Revisi -->
<div class="modal fade" id="modalRevision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dpl.self-proposals.revision', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title text-warning"><i class="la la-pencil-alt me-1"></i> Minta Revisi Usulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">
                        Tentukan catatan perbaikan yang harus dilakukan oleh mahasiswa <strong>{{ $proposal->student->user->name }}</strong>.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Perbaikan / Revisi: <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Jelaskan bagian yang perlu diperbaiki (cth: Perbaiki deskripsi ruang lingkup tugas agar sesuai dengan capaian prodi / unggah LoA yang bertanda tangan resmi)..." required>{{ old('notes', $proposal->dpl_notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Kirim Catatan Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tolak -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('dpl.self-proposals.reject', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title text-danger"><i class="la la-times-circle me-1"></i> Tolak Usulan Magang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">
                        Apakah Anda yakin ingin menolak usulan magang mandiri dari <strong>{{ $proposal->student->user->name }}</strong>?
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan: <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan usulan magang..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Usulan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
