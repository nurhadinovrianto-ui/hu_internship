@extends('layouts.app')

@section('title', 'Kuesioner Evaluasi Tempat Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kuesioner Pengalaman Magang</h4>
            <p class="mb-0">Evaluasi kenyamanan, bimbingan, dan relevansi tugas selama menjalani magang di mitra industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Kuesioner Evaluasi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Evaluasi Pengalaman Magang di Mitra</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Ulasan Anda bersifat rahasia dan dipergunakan program studi untuk mengevaluasi kelayakan kemitraan tempat magang di masa depan.</p>

                <form action="{{ route('student.surveys.store', $internship->id) }}" method="POST">
                    @csrf

                    @php
                        $ratings = [
                            5 => '5 - Sangat Baik / Sangat Mendukung',
                            4 => '4 - Baik / Memuaskan',
                            3 => '3 - Cukup / Standar',
                            2 => '2 - Kurang / Kurang Mendukung',
                            1 => '1 - Sangat Kurang / Tidak Nyaman',
                        ];
                    @endphp

                    @foreach($questions as $key => $label)
                        <div class="mb-4 border-bottom pb-3">
                            <label class="form-label fw-bold text-dark">{{ $loop->iteration }}. {{ $label }} <span class="text-danger">*</span></label>
                            <div class="row g-2 mt-1">
                                @foreach($ratings as $val => $desc)
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="{{ $key }}_rating" id="{{ $key }}_{{ $val }}" value="{{ $val }}" {{ (old($key.'_rating', $existing?->{$key.'_rating'} ?? 5) == $val) ? 'checked' : '' }} required>
                                            <label class="form-check-label" for="{{ $key }}_{{ $val }}">
                                                {{ $desc }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    <!-- REKOMENDASI -->
                    <div class="mb-4 border-bottom pb-3">
                        <label class="form-label fw-bold text-dark">6. Apakah Anda merekomendasikan perusahaan ini untuk adik tingkat Anda pada periode magang berikutnya? <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recommendation" id="rec-yes" value="1" {{ (old('recommendation', $existing?->recommendation ?? true) == 1) ? 'checked' : '' }} required>
                                <label class="form-check-label fw-medium text-success" for="rec-yes">
                                    <i class="la la-thumbs-up me-1"></i> Ya, Sangat Direkomendasikan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recommendation" id="rec-no" value="0" {{ (old('recommendation', $existing?->recommendation ?? true) == 0) ? 'checked' : '' }} required>
                                <label class="form-check-label fw-medium text-danger" for="rec-no">
                                    <i class="la la-thumbs-down me-1"></i> Tidak Direkomendasikan
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- FEEDBACK TEKS -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Testimoni, Pesan, atau Kendala yang Dihadapi (Opsional)</label>
                        <textarea name="feedback_text" class="form-control" rows="3" placeholder="Ceritakan pengalaman berharga atau kendala yang Anda alami di tempat magang...">{{ old('feedback_text', $existing?->feedback_text) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('student.dashboard') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-save me-1"></i> Simpan Evaluasi Pengalaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: MITRA INFO -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Mitra Magang Anda</h4>
            </div>
            <div class="card-body">
                <h5 class="text-dark fw-bold mb-1">{{ $internship->vacancy->industry->name }}</h5>
                <p class="text-muted mb-3"><i class="la la-map-marker text-primary"></i> {{ $internship->vacancy->industry->address }}</p>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Posisi Magang:</small>
                        <span class="text-dark fw-medium">{{ $internship->vacancy->title }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Dosen Pembimbing Lapangan:</small>
                        <span class="text-dark">{{ $internship->getDpl()?->user?->name ?? '-' }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
