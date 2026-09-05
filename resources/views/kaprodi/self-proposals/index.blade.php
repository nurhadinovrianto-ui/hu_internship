@extends('layouts.app')

@section('title', 'Verifikasi Magang Mandiri - Kaprodi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Verifikasi Magang Mandiri</h4>
            <p class="mb-0">Tinjau kesesuaian capaian pembelajaran (CPL) dari usulan magang inisiatif mandiri mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Magang Mandiri</a></li>
        </ol>
    </div>
</div>

<!-- 4 EDUMIN STAT CARDS -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-folder-open" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Usulan</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Semua Periode</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-user-check" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Disetujui DPL</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['dpl_approved'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Siap Finalisasi Kaprodi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-check-double" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Disetujui Resmi</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['approved'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Akun Mitra Dibuat</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-clock" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Menunggu / Revisi</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['submitted'] + $stats['revision'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Perlu Tindak Lanjut</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Daftar Usulan Magang Mandiri Prodi {{ $prodi?->name }}</h4>
            </div>

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

                <!-- Search & Filter Form -->
                <form action="{{ route('kaprodi.self-proposals.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama mahasiswa, NIM, perusahaan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Final</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Review</option>
                            <option value="dpl_approved" {{ request('status') === 'dpl_approved' ? 'selected' : '' }}>Disetujui DPL (Siap Sahkan)</option>
                            <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Perlu Revisi</option>
                            <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui Resmi</option>
                            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="dpl_status" class="form-control form-select">
                            <option value="">Semua Status Review DPL</option>
                            <option value="pending" {{ request('dpl_status') === 'pending' ? 'selected' : '' }}>DPL: Menunggu Review</option>
                            <option value="approved" {{ request('dpl_status') === 'approved' ? 'selected' : '' }}>DPL: Disetujui</option>
                            <option value="revision" {{ request('dpl_status') === 'revision' ? 'selected' : '' }}>DPL: Perlu Revisi</option>
                            <option value="rejected" {{ request('dpl_status') === 'rejected' ? 'selected' : '' }}>DPL: Ditolak</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'dpl_status']))
                            <a href="{{ route('kaprodi.self-proposals.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Perusahaan & Posisi</strong></th>
                                <th><strong>Dosen DPL</strong></th>
                                <th><strong>Review DPL</strong></th>
                                <th><strong>Status Akhir</strong></th>
                                <th><strong>Akun Mitra</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $prop)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $prop->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $prop->student->nim }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">{{ $prop->company_name }}</span>
                                        <br><small class="text-primary">{{ $prop->position_title }}</small>
                                    </td>
                                    <td>
                                        @if($prop->dpl)
                                            <span class="text-dark fw-medium">{{ $prop->dpl->user->name }}</span>
                                            <br><small class="text-muted">NIDN: {{ $prop->dpl->nidn }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Belum diplot</span>
                                        @endif
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
                                        @if($prop->partner_account_created)
                                            <span class="badge light badge-success">
                                                <i class="la la-key me-1"></i> Akun Aktif
                                            </span>
                                        @else
                                            <span class="badge light badge-secondary">Belum Dibuat</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('kaprodi.self-proposals.show', $prop->id) }}" class="btn btn-primary btn-sm px-3">
                                            <i class="la la-search me-1"></i> Tinjau
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="la la-folder-open mb-2" style="font-size: 36px;"></i>
                                        <p class="mb-0">Tidak ada pengajuan magang mandiri yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $proposals->firstItem() ?? 0 }} - {{ $proposals->lastItem() ?? 0 }} dari {{ $proposals->total() }} usulan
                    </small>
                    <div>
                        {{ $proposals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
