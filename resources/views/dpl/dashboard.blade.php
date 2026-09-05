@extends('layouts.app')

@section('title', 'DPL Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Dosen Pembimbing Lapangan (DPL)</h4>
            <p class="mb-0">Monitoring bimbingan mahasiswa magang aktif dan review logbook jurnal.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Stat Cards -->
    <div class="col-xl-4 col-xxl-4 col-sm-4">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-user-friends" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa Bimbingan</p>
                        <h3 class="text-white">{{ $stats['total_students'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-xxl-4 col-sm-4">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success p-3 rounded-circle">
                        <i class="la la-play-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_students'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-xxl-4 col-sm-4">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning p-3 rounded-circle">
                        <i class="la la-book-open" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Logbook Belum Direview</p>
                        <h3 class="text-white">{{ $stats['pending_logbooks'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pending Logbooks list -->
    <div class="col-lg-12">
        <div class="card shadow-sm">
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
                                <th><strong>Posisi Magang</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingLogbooks as $log)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark">{{ $log->student->user->name }}</h6>
                                        <small class="text-muted">{{ $log->student->nim }}</small>
                                    </td>
                                    <td>{{ $log->date->format('d M Y') }}</td>
                                    <td>{{ Str::limit($log->title, 40) }}</td>
                                    <td>{{ $log->internship->vacancy->title }}</td>
                                    <td>
                                        <a href="{{ route('dpl.logbooks.show', $log->id) }}" class="btn btn-primary btn-sm px-3">Review Jurnal</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada logbook baru yang membutuhkan review.</td>
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
