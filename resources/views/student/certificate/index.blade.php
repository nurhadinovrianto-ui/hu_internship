@extends('layouts.app')

@section('title', 'Sertifikat Magang Digital')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Unduh Sertifikat Magang Digital</h4>
            <p class="mb-0">Unduh sertifikat kelulusan magang resmi yang ditandatangani oleh Universitas.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Sertifikat</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-certificate text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Sertifikat Belum Tersedia</h3>
                    <p class="text-muted mx-auto" style="max-width: 600px; font-size: 15px; line-height: 1.6;">
                        {{ $reason }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 mt-3">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-5 text-center">
                    <i class="la la-award text-success mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Selamat, Magang Anda Selesai!</h3>
                    <p class="text-muted leading-relaxed" style="font-size: 14px;">
                        Nilai akhir magang Anda telah diverifikasi dan dikonversi oleh BAAK ke dalam SKS Akademik. 
                        Sertifikat digital resmi dengan nomor sertifikat di bawah ini kini siap untuk diunduh.
                    </p>
                    
                    <div class="alert alert-info border-0 text-center py-2 my-4" style="font-size: 13.5px; font-weight: 600; background-color: #F0F9FF; color: #0369A1;">
                        No. Sertifikat: {{ $certificate->certificate_number }}
                    </div>

                    <a href="{{ route('student.certificate.download') }}" class="btn btn-success text-white btn-block btn-lg py-3" style="font-weight: 700;">
                        <i class="la la-download me-1"></i> Unduh Sertifikat PDF
                    </a>
                </div>
            </div>
        </div>

        <!-- Grade Summary Card -->
        <div class="col-xl-6 col-lg-6 col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-5">
                    <h5 class="text-dark mb-4" style="font-weight: 700;">Ringkasan Nilai Akhir Magang</h5>
                    
                    <ul class="list-unstyled mb-0">
                        <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Nilai Rata-rata Industri (40%):</span>
                            <strong class="text-dark">{{ $conversion->industry_score }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Nilai Rata-rata DPL Akademik (60%):</span>
                            <strong class="text-dark">{{ $conversion->dpl_score }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Nilai Akhir Gabungan:</span>
                            <strong class="text-dark">{{ $conversion->final_score }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Nilai Mutu / Huruf:</span>
                            <strong class="text-success">{{ $conversion->letter_grade }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-3 border-bottom pb-2">
                            <span class="text-muted">Mata Kuliah Pengganti SKS:</span>
                            <strong class="text-dark">{{ $conversion->mata_kuliah_pengganti }}</strong>
                        </li>
                        <li class="d-flex justify-content-between mb-0">
                            <span class="text-muted">Bobot SKS Dikonversi:</span>
                            <strong class="text-primary">{{ $conversion->sks_converted }} SKS</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
