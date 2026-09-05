@extends('layouts.app')

@section('title', 'BAAK Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Bagian BAAK</h4>
            <p class="mb-0">Manajemen kelayakan akademik SKS dan konversi nilai magang mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">BAAK</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Stat Cards -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-check-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Mahasiswa SKS Eligible</p>
                        <h3 class="text-white">{{ $stats['sks_eligible'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning p-3 rounded-circle">
                        <i class="la la-user-clock" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Belum Terdata SKS</p>
                        <h3 class="text-white">{{ $stats['sks_pending'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-danger">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-danger p-3 rounded-circle">
                        <i class="la la-exchange-alt" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Konversi Pending</p>
                        <h3 class="text-white">{{ $stats['grade_pending'] }}</h3>
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
                        <i class="la la-certificate" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Konversi Sukses</p>
                        <h3 class="text-white">{{ $stats['grade_processed'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <i class="la la-file-alt text-primary mb-3" style="font-size: 56px;"></i>
                <h4 class="card-title" style="font-weight: 700;">Input SKS Mahasiswa</h4>
                <p class="text-muted">Masukkan data jumlah SKS mahasiswa sebelum magang untuk memvalidasi kelayakan akademik.</p>
                <a href="{{ route('baak.sks.index') }}" class="btn btn-primary px-4 mt-3">Mulai Validasi SKS</a>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body text-center p-5">
                <i class="la la-exchange-alt text-success mb-3" style="font-size: 56px;"></i>
                <h4 class="card-title" style="font-weight: 700;">Konversi Nilai Akhir</h4>
                <p class="text-muted">Proses nilai gabungan dari Pembimbing Akademik &amp; Industri menjadi SKS transkrip final.</p>
                <a href="{{ route('baak.grade-conversions.index') }}" class="btn btn-success text-white px-4 mt-3">Konversi Nilai</a>
            </div>
        </div>
    </div>
</div>
@endsection
