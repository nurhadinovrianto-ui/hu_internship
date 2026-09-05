@extends('layouts.app')

@section('title', 'Detail Jurnal Logbook')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Laporan Logbook</h4>
            <p class="mb-0">Periksa aktivitas harian dan jejak riwayat evaluasi pembimbing.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.logbooks.index') }}">Logbook</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Logbook Detail & Timeline (Left Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title mb-0">{{ $logbook->title }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge {{ $logbook->status_badge['class'] }} py-2 px-3">
                        {{ $logbook->status_badge['label'] }}
                    </span>
                    @if($logbook->status === 'revision_required')
                        <a href="{{ route('student.logbooks.edit', $logbook->id) }}" class="btn btn-warning btn-sm text-white">
                            <i class="la la-edit me-1"></i> Perbaiki &amp; Kirim Ulang
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Info Tanggal -->
                <div class="mb-4 pb-3 border-bottom text-muted" style="font-size: 13.5px;">
                    <i class="la la-calendar me-1"></i> Dilaporkan pada: <strong>{{ $logbook->date->translatedFormat('l, d F Y') }}</strong>
                </div>

                <!-- Deskripsi Aktivitas -->
                <div class="mb-4">
                    <h6 class="text-primary mb-2" style="font-weight: 700; font-size: 14px;">
                        <i class="la la-tasks me-2"></i>DESKRIPSI AKTIVITAS HARIAN
                    </h6>
                    <div class="p-3 rounded bg-light border" style="color: #334155; font-size: 14px; line-height: 1.7;">
                        {!! nl2br(e($logbook->description)) !!}
                    </div>
                </div>

                <!-- Hasil Pembelajaran -->
                @if($logbook->learning_outcomes)
                    <div class="mb-4">
                        <h6 class="text-primary mb-2" style="font-weight: 700; font-size: 14px;">
                            <i class="la la-lightbulb-o me-2"></i>HASIL PEMBELAJARAN &amp; KOMPETENSI
                        </h6>
                        <div class="p-3 rounded bg-primary bg-opacity-10 border border-primary border-opacity-25" style="color: #1e293b; font-size: 14px; line-height: 1.7;">
                            {!! nl2br(e($logbook->learning_outcomes)) !!}
                        </div>
                    </div>
                @endif
                
                <!-- Berkas Lampiran -->
                @if($logbook->attachment)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-dark mb-2" style="font-weight: 700; font-size: 14px;">
                            <i class="la la-paperclip me-1"></i> BERKAS LAMPIRAN PEKERJAAN
                        </h6>
                        <div class="d-flex align-items-center p-3 rounded border bg-light justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="la la-file-alt text-primary me-3" style="font-size: 28px;"></i>
                                <div>
                                    <h6 class="mb-0 text-dark" style="font-weight: 600; font-size: 13.5px;">Lampiran Aktivitas Logbook</h6>
                                    <small class="text-muted">Klik tombol untuk mengunduh dokumen kerja</small>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $logbook->attachment) }}" target="_blank" class="btn btn-primary btn-sm px-3">
                                <i class="la la-download me-1"></i> Unduh File
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Side (Timeline Edumin Template Style) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title mb-0">Jejak &amp; Riwayat Review</h4>
            </div>
            <div class="card-body">
                <div class="widget-timeline">
                    <ul class="timeline">
                        <!-- Step 1: Mahasiswa Mengirim -->
                        <li>
                            <div class="timeline-badge primary"></div>
                            <div class="timeline-panel text-muted">
                                <span>{{ $logbook->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                <h6 class="mb-1 text-dark" style="font-weight: 700;">Jurnal Dikirim</h6>
                                <p class="mb-0">
                                    Dilaporkan oleh <strong>{{ Auth::user()->name ?? 'Mahasiswa' }}</strong>
                                </p>
                            </div>
                        </li>

                        <!-- Steps 2+: Review dari DPL / Industri -->
                        @forelse($logbook->reviews->sortBy('created_at') as $rev)
                            @php
                                $isApproved = ($rev->status === 'approved');
                                $isRevision = ($rev->status === 'revision');
                                $badgeColorClass = $isApproved ? 'success' : ($isRevision ? 'danger' : 'warning');
                                $statusText = $isApproved ? 'DISETUJUI' : ($isRevision ? 'PERLU REVISI' : 'DICATAT');
                            @endphp
                            <li>
                                <div class="timeline-badge {{ $badgeColorClass }}"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $rev->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <h6 class="mb-0 text-dark" style="font-weight: 700;">{{ $rev->reviewer->name }}</h6>
                                        <span class="badge badge-xs badge-outline-secondary">
                                            {{ $rev->reviewer_type === 'industry' ? 'Pembimbing Industri' : 'DPL Kampus' }}
                                        </span>
                                        <span class="badge badge-xs bg-{{ $badgeColorClass }} text-white">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    <p class="mb-0 text-dark p-2 rounded bg-light border mt-1" style="font-size: 13px;">
                                        "{{ $rev->comment }}"
                                    </p>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="timeline-badge dark"></div>
                                <div class="timeline-panel text-muted">
                                    <h6 class="mb-1 text-muted" style="font-weight: 600;">Belum Ada Evaluasi</h6>
                                    <p class="mb-0">Menunggu peninjauan dari DPL maupun Pembimbing Industri.</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
