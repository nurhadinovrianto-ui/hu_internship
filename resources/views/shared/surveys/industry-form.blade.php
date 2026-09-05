@extends('layouts.app')

@section('title', 'Kuesioner Evaluasi Kepuasan Mitra Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kuesioner Evaluasi Kepuasan Mitra</h4>
            <p class="mb-0">Umpan balik penilaian kinerja dan kompetensi mahasiswa selama menjalani magang di perusahaan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('industry.dashboard') }}">Industri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Kuesioner Evaluasi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Instrumen Evaluasi Kinerja Mahasiswa</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">Penilaian ini bersifat objektif dan menjadi dasar evaluasi penjaminan mutu kurikulum program studi (Standar Akreditasi BAN-PT / LAM).</p>

                <form action="{{ route('industry.surveys.store', $internship->id) }}" method="POST">
                    @csrf

                    @php
                        $ratings = [
                            5 => '5 - Sangat Baik / Melebihi Ekspektasi',
                            4 => '4 - Baik / Sesuai Ekspektasi',
                            3 => '3 - Cukup / Memadai',
                            2 => '2 - Kurang / Butuh Peningkatan',
                            1 => '1 - Sangat Kurang / Tidak Memuaskan',
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
                        <label class="form-label fw-bold text-dark">6. Apakah perusahaan Anda bersedia menerima mahasiswa magang dari Universitas Horizon Indonesia pada periode berikutnya? <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4 mt-1">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recommendation" id="rec-yes" value="1" {{ (old('recommendation', $existing?->recommendation ?? true) == 1) ? 'checked' : '' }} required>
                                <label class="form-check-label fw-medium text-success" for="rec-yes">
                                    <i class="la la-thumbs-up me-1"></i> Ya, Sangat Bersedia
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="recommendation" id="rec-no" value="0" {{ (old('recommendation', $existing?->recommendation ?? true) == 0) ? 'checked' : '' }} required>
                                <label class="form-check-label fw-medium text-danger" for="rec-no">
                                    <i class="la la-thumbs-down me-1"></i> Tidak / Perlu Evaluasi Lanjut
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- FEEDBACK TEKS -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Kritik, Saran, atau Rekomendasi untuk Peningkatan Kurikulum Kampus</label>
                        <textarea name="feedback_text" class="form-control" rows="3" placeholder="Tuliskan masukan Anda terkait teknologi atau keahlian yang perlu diperkuat di perkuliahan...">{{ old('feedback_text', $existing?->feedback_text) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('industry.dashboard') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-save me-1"></i> Simpan Kuesioner Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: STUDENT CARD -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Mahasiswa yang Dinilai</h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $internship->student->photo_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $internship->student->user->name }}">
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">{{ $internship->student->user->name }}</h6>
                        <small class="text-muted">NIM: {{ $internship->student->nim }}</small><br>
                        <small class="text-muted">{{ $internship->student->studyProgram?->name }}</small>
                    </div>
                </div>

                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Posisi Magang:</small>
                        <strong class="text-dark">{{ $internship->vacancy->title }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Durasi Magang:</small>
                        <span class="text-dark">{{ $internship->start_date?->format('d M Y') }} s/d {{ $internship->end_date?->format('d M Y') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
