@extends('layouts.app')

@section('title', 'Logbook Jurnal Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Jurnal Logbook Magang Harian</h4>
            <p class="mb-0">Tulis dan laporkan aktivitas magang harian Anda ke DPL &amp; Supervisor Industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Logbook Jurnal</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-journal-whills text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Akses Jurnal Logbook Ditutup</h3>
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
        <div class="col-12">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body py-3">
                    <form action="{{ route('student.logbooks.index') }}" method="GET" class="row g-2 align-items-center">
                        <div class="col-md-4">
                            <label class="form-label mb-1 text-dark" style="font-size: 13px;">Cari Aktivitas / Judul</label>
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari judul atau isi jurnal..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 text-dark" style="font-size: 13px;">Filter Bulan</label>
                            <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label mb-1 text-dark" style="font-size: 13px;">Filter Status Review</label>
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Status --</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Terkirim / Menunggu</option>
                                <option value="reviewed_dpl" {{ request('status') == 'reviewed_dpl' ? 'selected' : '' }}>Direview DPL</option>
                                <option value="reviewed_industry" {{ request('status') == 'reviewed_industry' ? 'selected' : '' }}>Direview Industri</option>
                                <option value="revision_required" {{ request('status') == 'revision_required' ? 'selected' : '' }}>Perlu Revisi</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end mt-4 gap-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                <i class="la la-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'month', 'status']))
                                <a href="{{ route('student.logbooks.index') }}" class="btn btn-light btn-sm" title="Reset"><i class="la la-undo"></i></a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="card-title mb-1" style="font-weight: 700;">Daftar Riwayat Logbook Jurnal</h4>
                        <small class="text-muted">Perusahaan: {{ $internship->vacancy->industry->name ?? '-' }} (Periode: {{ $internship->start_date?->format('d/m/Y') }} - {{ $internship->end_date?->format('d/m/Y') }})</small>
                    </div>
                    <div>
                        @if($internship->status === 'active')
                            <a href="{{ route('student.logbooks.export', request()->all()) }}" class="btn btn-outline-primary px-4 me-2">
                                <i class="la la-download me-1"></i> Export Logbook
                            </a>
                            <a href="{{ route('student.logbooks.create') }}" class="btn btn-primary px-4">
                                <i class="la la-plus me-1"></i> Tulis Jurnal Baru
                            </a>
                        @else
                            <a href="{{ route('student.logbooks.export', request()->all()) }}" class="btn btn-outline-primary px-4 me-2">
                                <i class="la la-download me-1"></i> Export Logbook
                            </a>
                            <span class="badge badge-secondary py-2 px-3">
                                <i class="la la-history me-1"></i> Riwayat Magang Selesai (Baca Saja)
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Tanggal</strong></th>
                                    <th><strong>Judul Aktivitas</strong></th>
                                    <th><strong>Lampiran</strong></th>
                                    <th><strong>Status Review</strong></th>
                                    <th><strong>Aksi</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logbooks as $log)
                                    <tr>
                                        <td style="font-weight: 600;">{{ $log->date->translatedFormat('d M Y') }}</td>
                                        <td>{{ Str::limit($log->title, 50) }}</td>
                                        <td>
                                            @if($log->attachment)
                                                <a href="{{ asset('storage/' . $log->attachment) }}" target="_blank" class="badge badge-info text-white px-3 py-1">
                                                    <i class="la la-paperclip"></i> Lihat File
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $log->status_badge['class'] }}">
                                                {{ $log->status_badge['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('student.logbooks.show', $log->id) }}" class="btn btn-primary btn-sm px-3">Lihat Review</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada jurnal logbook yang ditulis.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($logbooks->hasPages() || $logbooks->total() > 0)
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Menampilkan {{ $logbooks->firstItem() ?? 0 }} - {{ $logbooks->lastItem() ?? 0 }} dari {{ $logbooks->total() }} data</small>
                            {{ $logbooks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
