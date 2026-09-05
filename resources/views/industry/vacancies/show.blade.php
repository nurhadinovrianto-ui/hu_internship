@extends('layouts.app')

@section('title', 'Detail Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Lowongan Magang</h4>
            <p class="mb-0">Periksa detail data kualifikasi lowongan kerja dan daftar pelamar aktif.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industry.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Detail Lowongan (Left Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-5">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="text-dark mb-1" style="font-weight: 700;">{{ $vacancy->title }}</h4>
                        <p class="text-muted mb-0">Divisi: {{ $vacancy->division ?? '-' }} &bull; Posisi: {{ $vacancy->position }}</p>
                    </div>
                    <span class="badge {{ $vacancy->status_badge['class'] }} py-2 px-3">
                        {{ $vacancy->status_badge['label'] }}
                    </span>
                </div>

                <h5 class="text-dark mb-3" style="font-weight: 700;">Deskripsi Pekerjaan</h5>
                <p class="text-muted leading-relaxed" style="font-size: 14.5px;">
                    {!! nl2br(e($vacancy->description)) !!}
                </p>

                <h5 class="text-dark mt-5 mb-3" style="font-weight: 700;">Kualifikasi &amp; Persyaratan</h5>
                <p class="text-muted leading-relaxed" style="font-size: 14.5px;">
                    {!! nl2br(e($vacancy->requirements)) !!}
                </p>
            </div>
        </div>
        
        <!-- Applicants Section -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title" style="font-weight: 700;">Pelamar Terdaftar (Lolos Validasi Kaprodi)</h4>
                <a href="{{ route('industry.applicants.index', $vacancy->id) }}" class="btn btn-outline-primary btn-sm px-3">Proses Seleksi Pelamar</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Nama Pelamar</strong></th>
                                <th><strong>NIM</strong></th>
                                <th><strong>Prodi</strong></th>
                                <th><strong>Status Seleksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacancy->applications as $app)
                                <tr>
                                    <td class="text-dark font-weight-bold">{{ $app->student->user->name }}</td>
                                    <td>{{ $app->student->nim }}</td>
                                    <td>{{ $app->student->studyProgram->name }}</td>
                                    <td>
                                        <span class="badge {{ $app->status_badge['class'] }}">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada pelamar yang melamar lowongan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacancies stats panel (Right Side) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px; background-color: #6366F1;">
            <div class="card-body p-4 text-white">
                <h5 class="text-white mb-4" style="font-weight: 700;">Rangkuman Kuota</h5>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Total Kuota:</span>
                        <strong class="text-white">{{ $vacancy->quota }} Orang</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Sisa Kuota Tersedia:</span>
                        <strong class="text-white">{{ $vacancy->remaining_quota }} Orang</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Model Kerja:</span>
                        <strong class="text-white">{{ $vacancy->work_type_label }}</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-0">
                        <span>Batas Pelamaran:</span>
                        <strong class="text-white">{{ $vacancy->apply_deadline->format('d M Y') }}</strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
