@extends('layouts.app')

@section('title', $vacancy->position . ' - ' . $vacancy->industry->name)

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Rincian Lowongan Magang Mitra</h4>
            <p class="mb-0">{{ $vacancy->position }} &bull; {{ $vacancy->industry->name }}</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.vacancies.index') }}">Cari Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Rincian</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Left Column: Details -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                    <div>
                        <span class="badge badge-primary px-3 py-1 font-w600 me-1">{{ $vacancy->work_type_label }}</span>
                        @if($vacancy->study_program_id)
                            <span class="badge badge-info px-3 py-1 font-w600">{{ $vacancy->studyProgram?->name }}</span>
                        @else
                            <span class="badge badge-secondary px-3 py-1">Terbuka untuk Semua Prodi</span>
                        @endif
                        <h2 class="text-dark font-w600 mt-2 mb-1">{{ $vacancy->position }}</h2>
                        <h5 class="text-muted font-w400 mb-0">{{ $vacancy->title }}</h5>
                    </div>
                    <div class="text-end">
                        <span class="badge {{ $vacancy->status_badge['class'] }} px-3 py-2 fs-6">
                            {{ $vacancy->status_badge['label'] }}
                        </span>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Key Specs Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Durasi</small>
                            <strong class="text-dark">{{ $vacancy->duration }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Kuota</small>
                            <strong class="text-dark">{{ $vacancy->accepted_count }} / {{ $vacancy->quota }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Deadline</small>
                            <strong class="text-dark">{{ $vacancy->apply_deadline?->format('d M Y') }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-3 col-6">
                        <div class="p-3 bg-light rounded text-center">
                            <small class="text-muted d-block">Lokasi</small>
                            <strong class="text-dark">{{ $vacancy->location ?? ($vacancy->industry->city ?? '-') }}</strong>
                        </div>
                    </div>
                </div>

                <h5 class="text-primary font-w600 mb-2"><i class="la la-align-left me-1"></i> Deskripsi Pekerjaan</h5>
                <div class="text-dark mb-4" style="line-height: 1.7;">
                    {!! nl2br(e($vacancy->description)) !!}
                </div>

                <h5 class="text-primary font-w600 mb-2"><i class="la la-check-square me-1"></i> Persyaratan & Kriteria Pelamar</h5>
                <div class="text-dark mb-4" style="line-height: 1.7;">
                    {!! nl2br(e($vacancy->requirements)) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Mitra Info & Recommendation Tool -->
    <div class="col-lg-4">
        <!-- Mitra Info -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title mb-0">Tentang Mitra Industri</h5>
            </div>
            <div class="card-body pt-3">
                <h6 class="text-dark font-w600 mb-1">{{ $vacancy->industry->name }}</h6>
                <p class="text-muted small mb-2"><i class="la la-map-marker me-1"></i> {{ $vacancy->industry->address ?? '-' }}</p>
                @if($vacancy->industry->website)
                    <p class="small mb-2"><i class="la la-globe me-1"></i> <a href="{{ $vacancy->industry->website }}" target="_blank">{{ $vacancy->industry->website }}</a></p>
                @endif
                <p class="small text-muted">{{ $vacancy->industry->description ?? '' }}</p>
            </div>
        </div>

        <!-- Rekomendasikan ke Mahasiswa Bimbingan -->
        <div class="card shadow-sm border-0" style="border-left: 4px solid #7367f0 !important;">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title text-primary mb-0"><i class="la la-share-alt me-1"></i> Rekomendasikan ke Bimbingan</h5>
            </div>
            <div class="card-body pt-3">
                <p class="text-muted small mb-3">Kirimkan info lowongan ini langsung via WhatsApp kepada mahasiswa bimbingan Anda yang belum memiliki tempat magang:</p>

                @if($unplacedMentees->count() > 0)
                    <div class="list-group list-group-flush mb-3">
                        @foreach($unplacedMentees as $m)
                            @php
                                $std = $m->student;
                                $phone = $std?->user?->phone ? preg_replace('/[^0-9]/', '', $std->user->phone) : null;
                                $encodedMsg = urlencode("Halo {$std?->user?->name},\n\nSaya merekomendasikan lowongan magang berikut untuk Anda:\nPosisi: {$vacancy->position}\nPerusahaan: {$vacancy->industry->name}\nBatas Lamar: " . ($vacancy->apply_deadline?->format('d M Y')) . "\n\nSilakan cek dan ajukan lamaran melalui SIMANG: " . route('student.vacancies.show', $vacancy->id) . "\n\nSemangat!");
                            @endphp
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-dark d-block" style="font-size: 0.9rem;">{{ $std?->user?->name }}</strong>
                                    <small class="text-muted">NIM: {{ $std?->nim }}</small>
                                </div>
                                @if($phone)
                                    <a href="https://wa.me/{{ $phone }}?text={{ $encodedMsg }}" target="_blank" class="btn btn-outline-success btn-xs">
                                        <i class="la la-whatsapp me-1"></i> Kirim WA
                                    </a>
                                @else
                                    <span class="badge badge-light text-muted">No WA kosong</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light py-2 px-3 small text-muted mb-3">
                        Semua mahasiswa bimbingan Anda saat ini sudah memiliki tempat magang atau belum ada mahasiswa pra-penempatan yang di-plot untuk Anda.
                    </div>
                @endif

                <!-- Salin Tautan Lowongan -->
                <div>
                    <label class="form-label small text-muted">Tautan Lowongan untuk Mahasiswa:</label>
                    <div class="input-group input-group-sm">
                        <input type="text" id="vacancyUrl" class="form-control" value="{{ route('student.vacancies.show', $vacancy->id) }}" readonly>
                        <button class="btn btn-outline-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('vacancyUrl').value); alert('Tautan lowongan berhasil disalin!');">
                            <i class="la la-copy"></i> Salin
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
