@extends('layouts.app')

@section('title', 'Laporan Akhir Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Laporan Akhir Magang</h4>
            <p class="mb-0">Unggah laporan akhir secara terpisah untuk Dosen Pembimbing Lapangan (DPL) dan Pembimbing Industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan Akhir</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-file-pdf text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Akses Unggah Laporan Ditutup</h3>
                    <p class="text-muted mx-auto" style="max-width: 600px; font-size: 15px; line-height: 1.6;">
                        {{ $reason }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 mt-3">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Nav Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-pills mb-3" id="reportTabs" role="tablist">
                <li class="nav-item me-2" role="presentation">
                    <button class="nav-link active px-4 py-3 font-weight-bold" id="dpl-tab" data-bs-toggle="pill" data-bs-target="#dpl-report" type="button" role="tab">
                        <i class="la la-university me-1"></i> Laporan Akademik (Ke DPL / Kampus)
                        @if($dplReport)
                            <span class="badge {{ str_replace('badge-', 'bg-', $dplReport->status_badge['class']) }} text-white ms-2 font-weight-bold">{{ $dplReport->status_badge['label'] }}</span>
                        @endif
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link px-4 py-3 font-weight-bold" id="industry-tab" data-bs-toggle="pill" data-bs-target="#industry-report" type="button" role="tab">
                        <i class="la la-laptop-code me-1"></i> Laporan Proyek/Software (Ke Industri)
                        @if($industryReport)
                            <span class="badge {{ str_replace('badge-', 'bg-', $industryReport->status_badge['class']) }} text-white ms-2 font-weight-bold">{{ $industryReport->status_badge['label'] }}</span>
                        @endif
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="reportTabsContent">
        <!-- TAB 1: DPL / KAMPUS -->
        <div class="tab-pane fade show active" id="dpl-report" role="tabpanel">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="p-3 bg-primary text-white rounded me-3" style="font-size: 24px;">
                                    <i class="la la-university"></i>
                                </span>
                                <div>
                                    <h5 class="text-dark mb-1" style="font-weight: 700;">Laporan Akhir Kampus (Ke DPL)</h5>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Laporan magang formal sesuai panduan akademik kampus.</p>
                                </div>
                            </div>

                            @if(!$dplReport || $dplReport->status === 'revision')
                                <form action="{{ route('student.report.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="report_type" value="dpl">
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="title_dpl">Judul Laporan Magang Kampus <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title_dpl" class="form-control" placeholder="Contoh: Laporan Kerja Praktik di PT..." value="{{ old('title', $dplReport?->title) }}" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label" for="report_file_dpl">File Laporan Kampus (Format PDF) <span class="text-danger">*</span></label>
                                        <input type="file" name="report_file" id="report_file_dpl" class="form-control" accept=".pdf" required>
                                        <small class="text-muted">Maksimal ukuran file PDF 10MB.</small>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-block btn-lg py-3 font-weight-bold">
                                        <i class="la la-file-upload me-1"></i> Unggah Laporan Kampus
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success border-0 py-4 text-center mb-0" style="background-color: #ECFDF5; color: #047857;">
                                    <i class="la la-check-circle me-1" style="font-size: 40px; display: block; margin-bottom: 8px;"></i>
                                    <h5>Laporan Kampus Berhasil Diunggah</h5>
                                    <p class="mb-0 mt-3" style="font-size: 13px;">Judul: <strong>{{ $dplReport->title }}</strong></p>
                                    <p class="mb-0" style="font-size: 13px;">Status: 
                                        <span class="badge {{ str_replace('badge-', 'bg-', $dplReport->status_badge['class']) }} text-white px-3 py-1 font-weight-bold" style="font-size: 11px;">
                                            {{ $dplReport->status_badge['label'] }}
                                        </span>
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ asset('storage/' . $dplReport->file_path) }}" target="_blank" class="btn btn-success text-white btn-sm px-4">
                                            <i class="la la-file-download me-1"></i> Lihat Dokumen Laporan
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Review DPL & Riwayat Revisian -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="text-dark mb-4 font-weight-bold">Catatan Review & Riwayat Revisi (DPL)</h5>

                            @if($dplReport && $dplReport->revisions && $dplReport->revisions->count() > 0)
                                <div class="timeline-revisions">
                                    @foreach($dplReport->revisions as $rev)
                                        <div class="p-3 mb-3 rounded border" style="background-color: #F8FAFC;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-primary text-white font-weight-bold">Versi {{ $rev->version }}</span>
                                                <span class="badge {{ str_replace('badge-', 'bg-', $rev->status_badge['class']) }} text-white font-weight-bold">
                                                    {{ $rev->status_badge['label'] }}
                                                </span>
                                            </div>
                                            <p class="mb-1 text-dark font-weight-bold" style="font-size: 13px;">{{ $rev->title }}</p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">{{ $rev->created_at->format('d M Y, H:i') }} WIB</small>
                                                <a href="{{ asset('storage/' . $rev->file_path) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 12px;">
                                                    <i class="la la-file-pdf"></i> Lihat File V{{ $rev->version }}
                                                </a>
                                            </div>
                                            @if($rev->feedback)
                                                <div class="p-2 rounded mt-2" style="background-color: #FEF3C7; border-left: 3px solid #F59E0B;">
                                                    <small class="d-block font-weight-bold text-dark">Catatan Revisi dari DPL:</small>
                                                    <small class="text-dark">{!! nl2br(e($rev->feedback)) !!}</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="la la-history d-block mb-3" style="font-size: 56px;"></i>
                                    <span style="font-size: 14px;">Belum ada riwayat pengunggahan laporan kampus.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: INDUSTRI / PROYEK SOFTWARE -->
        <div class="tab-pane fade" id="industry-report" role="tabpanel">
            <div class="row">
                <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
                    <div class="card shadow-sm border-0" style="border-radius: 12px;">
                        <div class="card-body p-5">
                            <div class="d-flex align-items-center mb-3">
                                <span class="p-3 bg-info text-white rounded me-3" style="font-size: 24px;">
                                    <i class="la la-laptop-code"></i>
                                </span>
                                <div>
                                    <h5 class="text-dark mb-1" style="font-weight: 700;">Laporan Proyek/Software (Ke Industri)</h5>
                                    <p class="text-muted mb-0" style="font-size: 13px;">Laporan progres teknis, pembuatan software, atau luaran magang di perusahaan.</p>
                                </div>
                            </div>

                            @if(!$industryReport || $industryReport->status === 'revision')
                                <form action="{{ route('student.report.upload') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="report_type" value="industry">
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="title_industry">Judul Proyek / Pembuatan Software <span class="text-danger">*</span></label>
                                        <input type="text" name="title" id="title_industry" class="form-control" placeholder="Contoh: Pembuatan Sistem Aplikasi KMS..." value="{{ old('title', $industryReport?->title) }}" required>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label" for="report_file_industry">File Laporan Proyek/Teknis (Format PDF) <span class="text-danger">*</span></label>
                                        <input type="file" name="report_file" id="report_file_industry" class="form-control" accept=".pdf" required>
                                        <small class="text-muted">Maksimal ukuran file PDF 10MB.</small>
                                    </div>

                                    <button type="submit" class="btn btn-info text-white btn-block btn-lg py-3 font-weight-bold">
                                        <i class="la la-file-upload me-1"></i> Unggah Laporan Industri
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-success border-0 py-4 text-center mb-0" style="background-color: #ECFDF5; color: #047857;">
                                    <i class="la la-check-circle me-1" style="font-size: 40px; display: block; margin-bottom: 8px;"></i>
                                    <h5>Laporan Proyek/Software Berhasil Diunggah</h5>
                                    <p class="mb-0 mt-3" style="font-size: 13px;">Judul: <strong>{{ $industryReport->title }}</strong></p>
                                    <p class="mb-0" style="font-size: 13px;">Status: 
                                        <span class="badge {{ str_replace('badge-', 'bg-', $industryReport->status_badge['class']) }} text-white px-3 py-1 font-weight-bold" style="font-size: 11px;">
                                            {{ $industryReport->status_badge['label'] }}
                                        </span>
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ asset('storage/' . $industryReport->file_path) }}" target="_blank" class="btn btn-success text-white btn-sm px-4">
                                            <i class="la la-file-download me-1"></i> Lihat Dokumen Laporan
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Review Industri & Riwayat Revisian -->
                <div class="col-xl-6 col-lg-6 col-md-12">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                        <div class="card-body p-4">
                            <h5 class="text-dark mb-4 font-weight-bold">Catatan Review & Riwayat Revisi (Industri)</h5>

                            @if($industryReport && $industryReport->revisions && $industryReport->revisions->count() > 0)
                                <div class="timeline-revisions">
                                    @foreach($industryReport->revisions as $rev)
                                        <div class="p-3 mb-3 rounded border" style="background-color: #F8FAFC;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-info text-white font-weight-bold">Versi {{ $rev->version }}</span>
                                                <span class="badge {{ str_replace('badge-', 'bg-', $rev->status_badge['class']) }} text-white font-weight-bold">
                                                    {{ $rev->status_badge['label'] }}
                                                </span>
                                            </div>
                                            <p class="mb-1 text-dark font-weight-bold" style="font-size: 13px;">{{ $rev->title }}</p>
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">{{ $rev->created_at->format('d M Y, H:i') }} WIB</small>
                                                <a href="{{ asset('storage/' . $rev->file_path) }}" target="_blank" class="text-info font-weight-bold" style="font-size: 12px;">
                                                    <i class="la la-file-pdf"></i> Lihat File V{{ $rev->version }}
                                                </a>
                                            </div>
                                            @if($rev->feedback)
                                                <div class="p-2 rounded mt-2" style="background-color: #FEF3C7; border-left: 3px solid #F59E0B;">
                                                    <small class="d-block font-weight-bold text-dark">Catatan Revisi dari Pembimbing Industri:</small>
                                                    <small class="text-dark">{!! nl2br(e($rev->feedback)) !!}</small>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5">
                                    <i class="la la-history d-block mb-3" style="font-size: 56px;"></i>
                                    <span style="font-size: 14px;">Belum ada riwayat pengunggahan laporan proyek industri.</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
