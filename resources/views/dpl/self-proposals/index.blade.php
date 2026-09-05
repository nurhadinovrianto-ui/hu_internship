@extends('layouts.app')

@section('title', 'Review Usulan Magang Mandiri Mahasiswa')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Review Usulan Magang Mandiri</h4>
            <p class="mb-0">Tinjau relevansi akademik dan persetujuan tempat magang inisiatif mandiri mahasiswa bimbingan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Usulan Magang Mandiri</a></li>
        </ol>
    </div>
</div>

<!-- Stats Summary Cards -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-paper-plane fs-1"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white opacity-75">Total Usulan</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-clock fs-1"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white opacity-75">Perlu Review DPL</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-check-circle fs-1"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white opacity-75">Disetujui DPL</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['approved'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info text-white shadow-sm border-0">
            <div class="card-body p-4">
                <div class="media">
                    <span class="me-3">
                        <i class="la la-sync-alt fs-1"></i>
                    </span>
                    <div class="media-body text-white">
                        <p class="mb-1 text-white opacity-75">Revisi / Ditolak</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['revision'] + $stats['rejected'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="la la-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="la la-exclamation-triangle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Filter & Search -->
                <form action="{{ route('dpl.self-proposals.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mahasiswa, NIM, perusahaan, posisi..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Review DPL</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review DPL</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui DPL</option>
                            <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Perlu Revisi</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('dpl.self-proposals.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Perusahaan & Posisi</strong></th>
                                <th><strong>Periode Magang</strong></th>
                                <th><strong>Narahubung Mitra</strong></th>
                                <th><strong>Status Review DPL</strong></th>
                                <th><strong>Status Akhir</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $prop)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm me-2 bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                                <i class="la la-user-graduate"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 text-dark fw-bold">{{ $prop->student->user->name }}</h6>
                                                <small class="text-muted">NIM: {{ $prop->student->nim }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $prop->company_name }}</h6>
                                        <small class="text-primary fw-medium">{{ $prop->position_title }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark d-block">{{ $prop->start_date->format('d M Y') }} s/d</small>
                                        <small class="text-dark">{{ $prop->end_date->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">{{ $prop->contact_person_name }}</span>
                                        <br><small class="text-muted">{{ $prop->contact_person_email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $prop->dpl_status_badge['class'] }}">
                                            {{ $prop->dpl_status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $prop->status_badge['class'] }}">
                                            {{ $prop->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dpl.self-proposals.show', $prop->id) }}" class="btn btn-primary btn-sm px-3">
                                            <i class="la la-search-plus me-1"></i> Tinjau
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="la la-inbox mb-2" style="font-size: 42px;"></i>
                                        <p class="mb-0">Tidak ada pengajuan usulan magang mandiri yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $proposals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
