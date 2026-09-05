@extends('layouts.app')

@section('title', 'Logbook Bimbingan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Jurnal Logbook Mahasiswa Bimbingan</h4>
            <p class="mb-0">Daftar laporan harian mahasiswa magang aktif untuk ditinjau dan direview.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Logbook Jurnal</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Card 1: Filter Pencarian -->
        <div class="card-body bg-white mb-3 p-0 shadow-sm border rounded">
            <div class="card-header pb-0">
                <h4 class="card-title mb-1">Filter Pencarian</h4>
            </div>
            <div class="card-body py-3">
                <form action="{{ route('dpl.logbooks.index') }}" method="GET" class="row align-items-end">
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label class="form-label mb-1 text-dark" style="font-size: 13px;">Cari Mahasiswa</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Nama mahasiswa..." value="{{ request('search') }}">
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-4 col-sm-6 mb-3">
                        <label class="form-label mb-1 text-dark" style="font-size: 13px;">Bulan</label>
                        <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3">
                        <label class="form-label mb-1 text-dark" style="font-size: 13px;">Riwayat Evaluasi</label>
                        <select name="history_tab" class="form-control form-control-sm">
                            <option value="">Semua Riwayat</option>
                            <option value="pending" {{ request('history_tab') === 'pending' ? 'selected' : '' }}>Belum Direview</option>
                            <option value="reviewed" {{ request('history_tab') === 'reviewed' ? 'selected' : '' }}>Sudah Direview</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-6 mb-3">
                        <label class="form-label mb-1 text-dark" style="font-size: 13px;">Status Jurnal</label>
                        <select name="status" class="form-control form-control-sm">
                            <option value="">Semua Status</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu</option>
                            <option value="reviewed_dpl" {{ request('status') === 'reviewed_dpl' ? 'selected' : '' }}>Direview DPL</option>
                            <option value="revision_required" {{ request('status') === 'revision_required' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-6 col-sm-12 mb-3 d-flex">
                        <button type="submit" class="btn btn-primary btn-sm px-3 mr-2">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request('search') || request('month') || request('history_tab') || request('status'))
                            <a href="{{ route('dpl.logbooks.index') }}" class="btn btn-light btn-sm px-3">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>

        <!-- Card 2: Tabel Data -->
        <div class="card">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="card-title mb-1">Daftar Riwayat Logbook Jurnal</h4>
                    <p class="mb-0 text-muted" style="font-size: 13px;">Menampilkan laporan aktivitas harian yang perlu ditinjau.</p>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover text-dark" style="color: #333;">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Tanggal</strong></th>
                                <th><strong>Judul Aktivitas</strong></th>
                                <th><strong>Status Review</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logbooks as $log)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600; font-size: 14.5px;">{{ $log->student->user->name }}</h6>
                                        <span class="text-muted" style="font-size: 12.5px;">NIM: {{ $log->student->nim }}</span>
                                    </td>
                                    <td style="font-weight: 600;">{{ $log->date->translatedFormat('d M Y') }}</td>
                                    <td>{{ Str::limit($log->title, 55) }}</td>
                                    <td>
                                        <span class="badge {{ $log->status_badge['class'] }} py-1 px-2" style="font-size: 11px;">
                                            {{ $log->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dpl.logbooks.show', $log->id) }}" class="btn btn-primary btn-sm px-3 shadow-xs">
                                            <i class="la la-search mr-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="la la-inbox text-muted mb-2" style="font-size: 48px;"></i>
                                        <br>Belum ada logbook jurnal harian mahasiswa yang masuk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $logbooks->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
