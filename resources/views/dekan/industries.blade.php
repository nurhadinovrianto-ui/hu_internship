@extends('layouts.app')

@section('title', 'Kemitraan Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kemitraan Industri Mitra Kampus</h4>
            <p class="mb-0">Daftar instansi, BUMN, dan perusahaan yang terikat kerjasama dengan Fakultas.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dekan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Mitra Industri</a></li>
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
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Bidang Industri</strong></th>
                                <th><strong>Lokasi Kantor</strong></th>
                                <th><strong>Tingkat Kemitraan</strong></th>
                                <th><strong>Masa MOU</strong></th>
                                <th><strong>Jumlah Lowongan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($industries as $ind)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $ind->logo_url }}" width="35" height="35" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <div>
                                                <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $ind->name }}</h6>
                                                <small class="text-muted">{{ $ind->website ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $ind->industry_type }}</td>
                                    <td>{{ $ind->city }}, {{ $ind->province }}</td>
                                    <td>
                                        <span class="badge {{ $ind->partnership_status !== 'none' ? 'badge-success' : 'badge-light' }}">
                                            {{ strtoupper($ind->partnership_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ind->mou_start_date && $ind->mou_end_date)
                                            {{ $ind->mou_start_date->format('d/m/Y') }} - {{ $ind->mou_end_date->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge light badge-primary px-3 py-1" style="font-weight: 700;">
                                            {{ $ind->vacancies_count }} Lowongan
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada mitra industri terdaftar.</td>
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
