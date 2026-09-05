@extends('layouts.app')

@section('title', 'Kaprodi Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Ketua Program Studi @if(isset($prodi) && $prodi)<span class="badge bg-primary text-white ms-2" style="font-size: 14px;">Prodi {{ $prodi->name }}</span>@endif</h4>
            <p class="mb-0">Persetujuan akademik magang dan penugasan Dosen Pembimbing Lapangan (DPL) khusus Program Studi Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Stat Cards -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning p-3 rounded-circle">
                        <i class="la la-file-signature" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Validasi Pending</p>
                        <h3 class="text-white">{{ $stats['pending_applications'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info p-3 rounded-circle">
                        <i class="la la-user-clock" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Plotting DPL Pending</p>
                        <h3 class="text-white">{{ $stats['waiting_dpl'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success p-3 rounded-circle">
                        <i class="la la-user-friends" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Magang Berjalan</p>
                        <h3 class="text-white">{{ $stats['active_internships'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-chalkboard-teacher" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">DPL Aktif</p>
                        <h3 class="text-white">{{ $stats['available_dpl'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Pending Applications Table -->
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Permintaan Validasi Akademik Magang</h4>
                <a href="{{ route('kaprodi.applications.index') }}" class="btn btn-outline-primary btn-sm px-3">Semua Pengajuan</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Lowongan Magang</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Diajukan Pada</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingApplications as $app)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0 text-dark">{{ $app->student->user->name }}</h6>
                                                <small class="text-muted">NIM: {{ $app->student->nim }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $app->vacancy->title }}</td>
                                    <td>{{ $app->vacancy->industry->name }}</td>
                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('kaprodi.applications.show', $app->id) }}" class="btn btn-primary btn-sm px-3">Review &amp; Validasi</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pengajuan magang yang perlu divalidasi.</td>
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
