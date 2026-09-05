@extends('layouts.app')

@section('title', 'Detail Progress Lamaran')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Progress Lamaran</h4>
            <p class="mb-0">Informasi lengkap status lamaran dan catatan review berkas.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.applications.index') }}">Lamaran Saya</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Progress Timeline (Left Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title" style="font-weight: 700;">Status Alur Pengajuan Magang</h4>
            </div>
            <div class="card-body p-4">
                <div class="widget-timeline">
                    <ul class="timeline">
                        <!-- Step 1: Diajukan -->
                        <li>
                            <div class="timeline-badge primary"></div>
                            <div class="timeline-panel text-muted">
                                <span>{{ $application->created_at->format('d M Y, H:i') }} WIB</span>
                                <h6 class="mb-1 text-dark" style="font-weight: 700;">Lamaran Terkirim</h6>
                                <p class="mb-0">Berkas pendaftaran, CV, dan dokumen pendukung berhasil diajukan ke sistem.</p>
                            </div>
                        </li>

                        <!-- Step 2: Validasi Kaprodi -->
                        <li>
                            @if(in_array($application->status, ['kaprodi_approved', 'industry_accepted', 'industry_rejected']))
                                <div class="timeline-badge success"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $application->kaprodi_reviewed_at ? $application->kaprodi_reviewed_at->format('d M Y, H:i') : '' }} WIB</span>
                                    <h6 class="mb-1 text-success" style="font-weight: 700;">Disetujui Akademik (Kaprodi)</h6>
                                    <p class="mb-0">Kaprodi menyetujui program magang. Catatan: {{ $application->kaprodi_notes ?? '-' }}</p>
                                </div>
                            @elseif($application->status === 'kaprodi_rejected')
                                <div class="timeline-badge danger"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $application->kaprodi_reviewed_at ? $application->kaprodi_reviewed_at->format('d M Y, H:i') : '' }} WIB</span>
                                    <h6 class="mb-1 text-danger" style="font-weight: 700;">Ditolak Akademik (Kaprodi)</h6>
                                    <p class="mb-0">Kaprodi menolak pengajuan ini. Alasan: <strong>{{ $application->kaprodi_notes }}</strong></p>
                                </div>
                            @else
                                <div class="timeline-badge warning"></div>
                                <div class="timeline-panel text-muted">
                                    <h6 class="mb-1 text-warning" style="font-weight: 700;">Menunggu Review Kaprodi</h6>
                                    <p class="mb-0">Berkas administrasi Anda sedang dalam antrean pemeriksaan Ketua Program Studi.</p>
                                </div>
                            @endif
                        </li>

                        <!-- Step 3: Seleksi Industri -->
                        <li>
                            @if(in_array($application->status, ['industry_accepted']))
                                <div class="timeline-badge success"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $application->industry_reviewed_at ? $application->industry_reviewed_at->format('d M Y, H:i') : '' }} WIB</span>
                                    <h6 class="mb-1 text-success" style="font-weight: 700;">Diterima Magang oleh Perusahaan</h6>
                                    <p class="mb-0">Selamat! Supervisor Industri menerima Anda untuk magang. Catatan: {{ $application->industry_notes ?? '-' }}</p>
                                </div>
                            @elseif($application->status === 'industry_rejected')
                                <div class="timeline-badge danger"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $application->industry_reviewed_at ? $application->industry_reviewed_at->format('d M Y, H:i') : '' }} WIB</span>
                                    <h6 class="mb-1 text-danger" style="font-weight: 700;">Ditolak oleh Perusahaan</h6>
                                    <p class="mb-0">Mitra Industri menolak lamaran Anda. Alasan: <strong>{{ $application->industry_notes }}</strong></p>
                                </div>
                            @elseif($application->status === 'kaprodi_approved')
                                <div class="timeline-badge warning"></div>
                                <div class="timeline-panel text-muted">
                                    <h6 class="mb-1 text-warning" style="font-weight: 700;">Sedang Diseleksi Industri</h6>
                                    <p class="mb-0">Berkas lamaran Anda sudah diserahkan ke pihak Supervisor Industri untuk direview.</p>
                                </div>
                            @else
                                <div class="timeline-badge dark"></div>
                                <div class="timeline-panel text-muted">
                                    <h6 class="mb-1 text-muted" style="font-weight: 600;">Seleksi Industri (Pending)</h6>
                                    <p class="mb-0">Menunggu persetujuan akademik (Kaprodi) terlebih dahulu.</p>
                                </div>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Details Card (Right Side) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Rincian Berkas Lamaran</h5>
                
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px;">Lowongan Magang:</span>
                    <strong class="text-dark" style="font-size: 15px;">{{ $application->vacancy->title }}</strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px;">Nama Perusahaan:</span>
                    <strong class="text-dark" style="font-size: 15px;">{{ $application->vacancy->industry->name }}</strong>
                </div>

                <div class="mb-4">
                    <span class="text-muted d-block" style="font-size: 12px;">Masa Magang:</span>
                    <strong class="text-dark">{{ $application->vacancy->duration_months }} Bulan</strong>
                </div>

                <div class="border-top pt-3">
                    <h6 class="text-dark mb-3" style="font-weight: 700;">File Terlampir</h6>
                    
                    <div class="d-flex align-items-center mb-2">
                        <i class="la la-file-pdf text-danger me-2" style="font-size: 24px;"></i>
                        <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" class="text-primary" style="font-weight: 600; font-size: 13px;">Download CV Mahasiswa</a>
                    </div>
                    
                    @if($application->motivation_letter)
                        <div class="d-flex align-items-center mb-2">
                            <i class="la la-file-pdf text-danger me-2" style="font-size: 24px;"></i>
                            <a href="{{ asset('storage/' . $application->motivation_letter) }}" target="_blank" class="text-primary" style="font-weight: 600; font-size: 13px;">Download Motivation Letter</a>
                        </div>
                    @endif
                </div>

                @if($application->cover_letter)
                    <div class="border-top pt-3 mt-3">
                        <span class="text-muted d-block mb-1" style="font-size: 12px;">Surat Pengantar:</span>
                        <p class="text-dark mb-0 bg-light p-3 rounded" style="font-size: 13px;">
                            {{ $application->cover_letter }}
                        </p>
                        <div class="border-top pt-3 mt-3">
                            <h6 class="text-primary mb-0 mt-3" style="font-weight: 700;">Status Terkini:</h6>
                            <p class="mb-0 mt-1" style="font-size: 14px;">{!! $application->status_badge['label'] ?? ucfirst($application->status) !!}</p>
                            
                            @if(in_array($application->status, ['kaprodi_approved', 'industry_accepted']))
                                <a href="{{ route('student.applications.letter', $application->id) }}" class="btn btn-sm btn-outline-primary mt-3">
                                    <i class="la la-download me-1"></i> Unduh Surat Pengantar Magang
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
