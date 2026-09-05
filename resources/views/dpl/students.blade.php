@extends('layouts.app')

@section('title', 'Daftar Bimbingan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Daftar Mahasiswa Bimbingan Magang</h4>
            <p class="mb-0">Daftar mahasiswa magang aktif yang berada di bawah bimbingan Anda akademik.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Daftar Bimbingan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Progress Waktu</strong></th>
                                <th><strong>Total Kehadiran</strong></th>
                                <th><strong>Status Magang</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assign)
                                @php $intern = $assign->internship; @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $intern->student->photo_url }}" width="35" height="35" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <div>
                                                <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                                <small class="text-muted">NIM: {{ $intern->student->nim }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->vacancy->industry->name }}</h6>
                                        <small class="text-muted">Posisi: {{ $intern->vacancy->title }}</small>
                                    </td>
                                    <td style="width: 200px;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 6px; border-radius: 4px; overflow: hidden; background-color: #E2E8F0;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $intern->progress_percentage }}%;" aria-valuenow="{{ $intern->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                            <span style="font-size: 12px; font-weight: bold; color: #1E293B;">{{ $intern->progress_percentage }}%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white font-weight-bold px-3 py-2" style="font-size: 13px; border-radius: 6px;">
                                            <i class="la la-calendar-check me-1"></i> {{ $intern->attendances->where('status', 'present')->count() }} Hari Kehadiran
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $intern->status_badge['class'] }}">
                                            {{ $intern->status_badge['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada mahasiswa magang aktif yang di-plot untuk Anda.</td>
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
