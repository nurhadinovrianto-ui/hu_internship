@extends('layouts.app')

@section('title', 'Finance Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Bagian Keuangan</h4>
            <p class="mb-0">Verifikasi kelayakan administratif pembayaran SPP mahasiswa magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Finance</a></li>
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
                        <i class="la la-user-graduate" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa</p>
                        <h3 class="text-white">{{ $stats['total_students'] }}</h3>
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
                        <i class="la la-check-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Pembayaran Lunas</p>
                        <h3 class="text-white">{{ $stats['payment_cleared'] }}</h3>
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
                        <i class="la la-exclamation-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Pembayaran Pending</p>
                        <h3 class="text-white">{{ $stats['payment_pending'] }}</h3>
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
                        <i class="la la-user-minus" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Belum Terdaftar</p>
                        <h3 class="text-white">{{ $stats['not_registered'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Verifikasi Pembayaran Terbaru (Lunas)</h4>
                <a href="{{ route('finance.payments.index') }}" class="btn btn-outline-primary btn-sm px-3">Semua Pembayaran</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>NIM</strong></th>
                                <th><strong>Nama Mahasiswa</strong></th>
                                <th><strong>Program Studi</strong></th>
                                <th><strong>Diverifikasi Pada</strong></th>
                                <th><strong>Diverifikasi Oleh</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentVerified as $req)
                                <tr>
                                    <td>{{ $req->student->nim }}</td>
                                    <td>{{ $req->student->user->name }}</td>
                                    <td>{{ $req->student->studyProgram->name }}</td>
                                    <td>{{ $req->payment_verified_at->format('d M Y') }}</td>
                                    <td>{{ $req->paymentVerifier->name }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada verifikasi pembayaran terbaru.</td>
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
