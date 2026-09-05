@extends('layouts.app')

@section('title', 'Tinjau Usulan Magang Mandiri - Kaprodi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Verifikasi Usulan Magang Mandiri</h4>
            <p class="mb-0">Persetujuan institusional dan pembuatan otomatis akun Supervisor Industri mitra.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Kaprodi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.self-proposals.index') }}">Magang Mandiri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Tinjau</a></li>
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
    <!-- Kolom Kiri: Rincian Usulan & Rekomendasi DPL -->
    <div class="col-xl-8 col-lg-7">
        <!-- Rekomendasi DPL Banner -->
        @if($proposal->dpl)
            <div class="card shadow-sm border-0 mb-4 {{ $proposal->dpl_status === 'approved' ? 'border-start border-success border-4' : ($proposal->dpl_status === 'revision' ? 'border-start border-warning border-4' : ($proposal->dpl_status === 'rejected' ? 'border-start border-danger border-4' : 'border-start border-info border-4')) }}">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <i class="la la-user-tie fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-dark fw-bold">Evaluasi Dosen DPL: {{ $proposal->dpl->user->name }}</h6>
                                <small class="text-muted">NIDN: {{ $proposal->dpl->nidn }}</small>
                            </div>
                        </div>
                        <span class="badge {{ $proposal->dpl_status_badge['class'] }}">
                            {{ $proposal->dpl_status_badge['label'] }}
                        </span>
                    </div>
                    @if($proposal->dpl_notes)
                        <div class="p-2 bg-light rounded text-dark mt-2 small">
                            <strong>Catatan DPL:</strong> {{ $proposal->dpl_notes }}
                            @if($proposal->dpl_reviewed_at)
                                <span class="text-muted d-block mt-1">Ditinjau pada: {{ $proposal->dpl_reviewed_at->format('d M Y, H:i') }}</span>
                            @endif
                        </div>
                    @else
                        <small class="text-muted fst-italic">Dosen DPL belum mengisi catatan evaluasi.</small>
                    @endif
                </div>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <span class="badge {{ $proposal->status_badge['class'] }} mb-2">
                        Status Final: {{ $proposal->status_badge['label'] }}
                    </span>
                    <h4 class="card-title text-dark fw-bold mb-1">{{ $proposal->company_name }}</h4>
                    <p class="text-muted mb-0"><i class="la la-briefcase text-primary"></i> Posisi: <strong>{{ $proposal->position_title }}</strong></p>
                </div>
                <small class="text-muted">Diajukan: {{ $proposal->created_at->format('d M Y, H:i') }}</small>
            </div>

            <div class="card-body">
                @if($proposal->kaprodi_notes)
                    <div class="alert alert-info border-0 mb-4">
                        <h6 class="fw-bold mb-1"><i class="la la-info-circle me-1"></i> Catatan Verifikasi Kaprodi:</h6>
                        <p class="mb-0">{{ $proposal->kaprodi_notes }}</p>
                    </div>
                @endif

                <h6 class="text-primary fw-bold mb-2">Rencana Deskripsi Tugas / Jobdesk:</h6>
                <div class="p-3 bg-light rounded mb-4 text-dark" style="white-space: pre-line;">{{ $proposal->job_description }}</div>

                <div class="row g-3 mb-4">
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
                        <small class="text-muted d-block">Koordinat & Radius Geofence:</small>
                        <span class="text-dark fw-medium">{{ $proposal->latitude ?? '-' }}, {{ $proposal->longitude ?? '-' }} (Radius: {{ $proposal->geofence_radius }}m)</span>
                    </div>
                </div>

                <div class="border-top pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="text-primary fw-bold mb-1">Dokumen Bukti Penerimaan (Letter of Acceptance):</h6>
                        <small class="text-muted">Berkas tanda terima resmi dari perusahaan mitra.</small>
                    </div>
                    <a href="{{ asset('storage/' . $proposal->loa_file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                        <i class="la la-file-pdf me-1"></i> Buka Dokumen LoA
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Mahasiswa, Narahubung Mitra, Kredensial, & Aksi Kaprodi -->
    <div class="col-xl-4 col-lg-5">
        <!-- Profil Mahasiswa -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Data Mahasiswa Pengusul</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $proposal->student->photo_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $proposal->student->user->name }}">
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">{{ $proposal->student->user->name }}</h6>
                        <small class="text-muted">NIM: {{ $proposal->student->nim }}</small><br>
                        <small class="text-primary">{{ $proposal->student->studyProgram?->name }}</small>
                    </div>
                </div>

                <ul class="list-group list-group-flush mb-0 small">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">Email:</span>
                        <strong class="text-dark">{{ $proposal->student->user->email }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="text-muted">No. Telepon:</span>
                        <strong class="text-dark">{{ $proposal->student->phone ?? '-' }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Card Kredensial Akun Mitra Otomatis (Jika Disetujui) -->
        @if($proposal->status === 'approved' && $proposal->partner_account_created)
            <div class="card shadow-sm border-0 mb-4 border-start border-success border-4">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title text-success">
                        <i class="la la-key me-1"></i> Kredensial Akun Mitra (Aktif)
                    </h5>
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-2">
                        Akun login resmi Supervisor Industri telah dibuatkan secara otomatis oleh sistem:
                    </p>
                    <div class="p-3 bg-light rounded mb-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Email Login:</small>
                            <span class="text-dark fw-bold">{{ $proposal->contact_person_email }}</span>
                        </div>
                        <div>
                            <small class="text-muted d-block">Password Awal:</small>
                            <div class="input-group input-group-sm">
                                <input type="text" id="kaprodi-pass-input" class="form-control form-control-sm bg-white" value="{{ $proposal->partner_temp_password ?? '(Akun sudah ada)' }}" readonly>
                                <button class="btn btn-primary btn-sm" type="button" onclick="copyPassKaprodi()">
                                    <i class="la la-copy"></i> Salin
                                </button>
                            </div>
                        </div>
                    </div>
                    <small class="text-success"><i class="la la-check-circle"></i> Mahasiswa & pembimbing mitra dapat menggunakan kredensial ini.</small>
                </div>
            </div>
        @endif

        <!-- Narahubung Perusahaan -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Data Narahubung Mitra</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush mb-0 small">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Nama Lengkap:</small>
                        <strong class="text-dark">{{ $proposal->contact_person_name }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Jabatan:</small>
                        <span class="text-dark">{{ $proposal->contact_person_position ?? 'Mentor' }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Email Narahubung:</small>
                        <span class="text-primary fw-medium">{{ $proposal->contact_person_email }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">No. Telepon / WA:</small>
                        <span class="text-dark">{{ $proposal->contact_person_phone ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tombol Tindakan Kaprodi -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-dark">Keputusan Kaprodi</h5>
            </div>
            <div class="card-body">
                @if($proposal->status !== 'approved')
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove">
                            <i class="la la-check-circle me-1"></i> Setujui Usulan & Buat Akun Mitra
                        </button>
                        <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalRevision">
                            <i class="la la-edit me-1"></i> Minta Revisi Usulan
                        </button>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalReject">
                            <i class="la la-times-circle me-1"></i> Tolak Usulan
                        </button>
                    </div>
                @else
                    <div class="alert alert-success border-0 text-center mb-0">
                        <i class="la la-check-double mb-1 fs-3"></i>
                        <h6 class="mb-1 fw-bold text-success">Usulan Telah Disetujui</h6>
                        <small class="text-muted d-block">Program magang telah berstatus aktif dan akun pembimbing mitra telah dibuat.</small>
                    </div>
                @endif
                <div class="mt-3">
                    <a href="{{ route('kaprodi.self-proposals.index') }}" class="btn btn-light w-100">
                        <i class="la la-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Approve -->
<div class="modal fade" id="modalApprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kaprodi.self-proposals.approve', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0">
                    <h5 class="modal-title text-success"><i class="la la-check-circle me-1"></i> Persetujuan Final Magang Mandiri</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-dark">
                        Apakah Anda menyetujui usulan magang mandiri di <strong>{{ $proposal->company_name }}</strong> untuk mahasiswa <strong>{{ $proposal->student->user->name }}</strong>?
                    </p>
                    
                    <div class="alert alert-primary py-2 px-3 small mb-3">
                        <h6 class="fw-bold mb-1"><i class="la la-cog me-1"></i> Otomasi Sistem Saat Disetujui:</h6>
                        <ul class="mb-0 ps-3">
                            <li>Membuat/menghubungkan profil Industri mitra.</li>
                            <li><strong>Membuat akun login Supervisor Industri</strong> secara otomatis (Email: <code>{{ $proposal->contact_person_email }}</code>).</li>
                            <li>Mengaktifkan program magang mahasiswa dan menghubungkan Dosen DPL pembimbing.</li>
                        </ul>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Catatan Rekomendasi Kaprodi (Opsional):</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Catatan untuk mahasiswa, DPL, atau pembimbing industri...">{{ old('notes', $proposal->kaprodi_notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Konfirmasi & Sahkan Magang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Revision -->
<div class="modal fade" id="modalRevision" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kaprodi.self-proposals.revision', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0">
                    <h5 class="modal-title text-warning"><i class="la la-edit me-1"></i> Minta Revisi Usulan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Poin yang Perlu Direvisi: <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" required placeholder="Jelaskan hal-hal yang perlu diperbaiki oleh mahasiswa...">{{ old('notes', $proposal->kaprodi_notes) }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-white">Kirim Catatan Revisi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="modalReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('kaprodi.self-proposals.reject', $proposal->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger"><i class="la la-times-circle me-1"></i> Tolak Usulan Magang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold">Alasan Penolakan: <span class="text-danger">*</span></label>
                        <textarea name="notes" class="form-control" rows="4" required placeholder="Jelaskan alasan penolakan usulan magang ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function copyPassKaprodi() {
        const passInput = document.getElementById('kaprodi-pass-input');
        if (passInput) {
            passInput.select();
            passInput.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(passInput.value);
            alert('Password akun mitra berhasil disalin!');
        }
    }
</script>
@endsection
