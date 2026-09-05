@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<style>
    .bg-maroon { background-color: #800000 !important; color: #fff !important; }
    .alert-maroon { background-color: #800000 !important; color: #fff !important; border-color: #800000 !important; }
    .alert-maroon strong, .alert-maroon a { color: #fff !important; }
</style>
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Selamat Datang, {{ $student->user->name }}!</h4>
            <p class="mb-0">Portal Dashboard Magang Mahasiswa Horizon University</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

<!-- GATEKEEPER & ELIGIBILITY STATUS -->
@if(!$stats['has_active_internship'])
<div class="row">
    <div class="col-12">
        <h4 class="mb-4"><i class="la la-shield-alt text-primary me-2"></i> Syarat Pendaftaran Magang</h4>
    </div>
    <div class="col-xl-6 col-xxl-6 col-sm-6">
        <div class="widget-stat card {{ ($requirement && $requirement->payment_cleared) ? 'bg-maroon' : 'bg-danger' }}">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-money-bill"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Administrasi Keuangan</p>
                        <h4 class="text-white">
                            @if($requirement && $requirement->payment_cleared)
                                Terverifikasi
                            @else
                                Belum Terverifikasi
                            @endif
                        </h4>
                        <small>
                            @if($requirement && $requirement->payment_cleared)
                                Telah divalidasi oleh Finance
                            @else
                                Segera selesaikan pembayaran
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-xxl-6 col-sm-6">
        <div class="widget-stat card {{ ($requirement && $requirement->sks_eligible) ? 'bg-maroon' : 'bg-danger' }}">
            <div class="card-body">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-graduation-cap"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">SKS Minimum (BAAK)</p>
                        <h4 class="text-white">
                            @if($requirement && $requirement->sks_eligible)
                                Memenuhi Syarat
                            @else
                                Belum Memenuhi
                            @endif
                        </h4>
                        <small>
                            SKS Lulus: {{ $requirement ? $requirement->sks_completed : $student->total_sks }} / Min: {{ $requirement ? $requirement->sks_minimum : 100 }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        @if($student->isEligibleForInternship($period?->id))
            <div class="alert alert-maroon solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                <strong>Selamat!</strong> Anda memenuhi semua syarat administrasi. Silakan cari lowongan dan ajukan magang.
                <a href="{{ route('student.vacancies.browse') }}" class="btn btn-sm btn-light float-end mt-n1" style="color: #800000 !important;">Cari Lowongan</a>
            </div>
        @else
            <div class="alert alert-danger solid alert-dismissible fade show">
                <svg viewBox="0 0 24 24" width="24 " height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                <strong>Perhatian!</strong> Anda belum bisa mendaftar magang karena syarat belum terpenuhi.
            </div>
        @endif
    </div>
</div>
@endif

<!-- ACTIVE INTERNSHIP STATUS & QUICK ACTION -->
@if($stats['has_active_internship'])
    @php $internship = $stats['active_internship']; @endphp
    <div class="row">
        <div class="col-xl-12">
            <div class="card overflow-hidden">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title text-white"><i class="la la-briefcase me-2"></i> Program Magang Berjalan</h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mt-2 mb-1">{{ $internship->vacancy->title }}</h3>
                            <p class="text-muted mb-0">Divisi {{ $internship->vacancy->division ?? '-' }} di <strong>{{ $internship->vacancy->industry->name }}</strong></p>
                            <div class="mt-3">
                                <span class="badge badge-lg badge-success"><i class="la la-user-tie me-1"></i> DPL: {{ $internship->getDpl() ? $internship->getDpl()->user->name : 'Menunggu DPL' }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('student.attendance.index') }}" class="btn btn-primary d-block mb-2">
                                <i class="la la-user-check me-1"></i> Presensi Harian
                            </a>
                            <a href="{{ route('student.logbooks.create') }}" class="btn btn-outline-primary d-block">
                                <i class="la la-journal-whills me-1"></i> Isi Logbook
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- MY APPLICATIONS HISTORY -->
<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Riwayat Lamaran Magang Terakhir</h4>
                @if($student->isEligibleForInternship($period?->id) && !$stats['has_active_internship'])
                    <a href="{{ route('student.vacancies.browse') }}" class="btn btn-primary btn-sm">Cari Lowongan Lain</a>
                @endif
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-responsive-sm">
                        <thead>
                            <tr>
                                <th><strong>Lowongan</strong></th>
                                <th><strong>Perusahaan</strong></th>
                                <th><strong>Tanggal Daftar</strong></th>
                                <th><strong>Status Lamaran</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td><h6 class="mb-0">{{ $app->vacancy->title }}</h6></td>
                                    <td>{{ $app->vacancy->industry->name }}</td>
                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge badge-rounded {{ str_replace('bg-', 'badge-', $app->status_badge['class']) }}">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('student.applications.show', $app->id) }}" class="btn btn-primary btn-xs">Lihat Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat lamaran magang.</td>
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
