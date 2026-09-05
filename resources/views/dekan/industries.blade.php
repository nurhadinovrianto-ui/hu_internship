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
                <!-- Search & Filters -->
                <form action="{{ route('dekan.industries') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mitra, kota, atau bidang industri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="partnership_status" class="form-control form-select">
                            <option value="">Semua Tingkat Kemitraan</option>
                            <option value="mou" {{ request('partnership_status') === 'mou' ? 'selected' : '' }}>MOU (Aktif)</option>
                            <option value="moa" {{ request('partnership_status') === 'moa' ? 'selected' : '' }}>MOA</option>
                            <option value="none" {{ request('partnership_status') === 'none' ? 'selected' : '' }}>Tanpa Kerjasama</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'partnership_status']))
                            <a href="{{ route('dekan.industries') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

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

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $industries->firstItem() ?? 0 }} - {{ $industries->lastItem() ?? 0 }} dari {{ $industries->total() }} mitra industri
                    </small>
                    <div>
                        {{ $industries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
