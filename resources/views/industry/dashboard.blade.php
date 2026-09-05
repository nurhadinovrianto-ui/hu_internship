@extends('layouts.app')

@section('title', 'Industry Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Supervisor Industri</h4>
            <p class="mb-0">Publikasi lowongan, seleksi kandidat magang, review logbook harian, dan penilaian industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
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
                        <i class="la la-clipboard-list" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Lowongan</p>
                        <h3 class="text-white">{{ $stats['total_vacancies'] }}</h3>
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
                        <i class="la la-user-clock" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Kandidat Menunggu Seleksi</p>
                        <h3 class="text-white">{{ $stats['pending_applicants'] }}</h3>
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
                        <i class="la la-user-graduate" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Mahasiswa Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_interns'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Candidates Selection Table -->
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Pelamar Magang Masuk (Lolos Validasi Kaprodi)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Nama Kandidat</strong></th>
                                <th><strong>Program Studi</strong></th>
                                <th><strong>Lowongan Dilamar</strong></th>
                                <th><strong>Diajukan Pada</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $app)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark">{{ $app->student->user->name }}</h6>
                                        <small class="text-muted">GPA: {{ $app->student->gpa }} | NIM: {{ $app->student->nim }}</small>
                                    </td>
                                    <td>{{ $app->student->studyProgram->name }}</td>
                                    <td>{{ $app->vacancy->title }}</td>
                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                    <td>
                                        <a href="{{ route('industry.applicants.index', $app->vacancy_id) }}" class="btn btn-primary btn-sm px-3">Review Kandidat</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pelamar baru yang siap diseleksi.</td>
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
