@extends('layouts.app')

@section('title', 'DPL Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Dosen Pembimbing Lapangan (DPL)</h4>
            <p class="mb-0">Monitoring bimbingan mahasiswa magang aktif dan dampingi pencarian tempat magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

@include('shared.dashboard-period-filter')

<div class="row">
    <!-- Stat Cards -->
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="widget-stat card bg-primary shadow-sm h-100">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-user-friends" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa Bimbingan</p>
                        <h3 class="text-white">{{ $stats['total_students'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">{{ $period ? 'Periode Ini' : 'Semua Periode' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="widget-stat card bg-success shadow-sm h-100">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success p-3 rounded-circle">
                        <i class="la la-play-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Sedang Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_students'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Sudah ditempatkan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="widget-stat card bg-info shadow-sm h-100">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info p-3 rounded-circle">
                        <i class="la la-search-location" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Mencari Tempat Magang</p>
                        <h3 class="text-white">{{ $stats['pre_placement_students'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Pra-penempatan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="widget-stat card bg-warning shadow-sm h-100">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning p-3 rounded-circle">
                        <i class="la la-book" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Logbook Perlu Review</p>
                        <h3 class="text-white">{{ $stats['pending_logbooks'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Menunggu approval</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mahasiswa Bimbingan yang Belum Ditempatkan -->
@if($stats['pre_placement_students'] > 0)
    <div class="row mt-3">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-left: 4px solid #00cfe8 !important;">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="card-title text-info mb-1"><i class="la la-user-clock me-1"></i> Mahasiswa Bimbingan Belum Mendapatkan Tempat Magang ({{ $stats['pre_placement_students'] }})</h4>
                        <p class="text-muted mb-0 small">Mahasiswa berikut telah ditugaskan kepada Anda sebelum penempatan. Anda dapat mendampingi, mengarahkan, dan membantu mencarikan tempat magang mitra.</p>
                    </div>
                    <a href="{{ route('dpl.vacancies.index') }}" class="btn btn-info text-white btn-sm px-3">
                        <i class="la la-search-location me-1"></i> Cari Lowongan Mitra untuk Bimbingan
                    </a>
                </div>
                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Mahasiswa</strong></th>
                                    <th><strong>Program Studi</strong></th>
                                    <th><strong>Status Lamaran Saat Ini</strong></th>
                                    <th><strong>Kontak & Berkas</strong></th>
                                    <th class="text-end"><strong>Aksi Bimbingan</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prePlacementAssignments as $assign)
                                    @php
                                        $student = $assign->student;
                                        $apps = $student?->applications ?? collect();
                                        $activeApps = $apps->whereIn('status', ['pending', 'kaprodi_approved']);
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $student?->photo_url }}" width="38" height="38" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                                <div>
                                                    <h6 class="mb-0 text-dark font-w600">{{ $student?->user?->name ?? 'Mahasiswa' }}</h6>
                                                    <small class="text-muted">NIM: {{ $student?->nim ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $student?->studyProgram?->name ?? '-' }}</span>
                                            <small class="d-block text-muted">IPK: {{ $student?->gpa ?? '-' }} &bull; SKS: {{ $student?->total_sks ?? 0 }}</small>
                                        </td>
                                        <td>
                                            @if($activeApps->count() > 0)
                                                <span class="badge badge-outline-primary">
                                                    Sedang Melamar di {{ $activeApps->count() }} Perusahaan
                                                </span>
                                                <small class="d-block text-muted mt-1">
                                                    Terakhir: {{ $activeApps->first()?->vacancy?->industry?->name }}
                                                </small>
                                            @else
                                                <span class="badge badge-warning text-dark">
                                                    <i class="la la-exclamation-circle me-1"></i> Belum Mengajukan Lamaran
                                                </span>
                                                <small class="d-block text-muted mt-1">Perlu dibantu referensi tempat magang</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 flex-wrap align-items-center">
                                                @if($student?->user?->phone)
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->user->phone) }}" target="_blank" class="btn btn-outline-success btn-xs" title="Chat WhatsApp">
                                                        <i class="la la-whatsapp"></i> WhatsApp
                                                    </a>
                                                @endif
                                                @if($student?->cv_file)
                                                    <a href="{{ $student->cv_url }}" target="_blank" class="btn btn-outline-primary btn-xs" title="Lihat CV">
                                                        <i class="la la-file-pdf"></i> CV
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('dpl.vacancies.index') }}" class="btn btn-outline-info btn-sm">
                                                <i class="la la-search-location me-1"></i> Rekomendasikan Lowongan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="row mt-3">
    <!-- Pending Logbooks list -->
    <div class="col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Jurnal Logbook Masuk Perlu Review</h4>
                <a href="{{ route('dpl.logbooks.index') }}" class="btn btn-outline-primary btn-sm px-3">Semua Logbook</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Tanggal</strong></th>
                                <th><strong>Judul Aktivitas</strong></th>
                                <th><strong>Perusahaan</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLogbooks as $logbook)
                                <tr>
                                    <td>{{ $logbook->student->user->name }}</td>
                                    <td>{{ $logbook->date->format('d M Y') }}</td>
                                    <td>{{ $logbook->activity_title }}</td>
                                    <td>{{ $logbook->internship->vacancy->industry->name ?? '-' }}</td>
                                    <td><span class="badge {{ $logbook->status_badge['class'] }}">{{ $logbook->status_badge['label'] }}</span></td>
                                    <td>
                                        <a href="{{ route('dpl.logbooks.show', $logbook->id) }}" class="btn btn-primary btn-sm">Review</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-3 text-muted">Tidak ada logbook yang perlu direview saat ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
