@extends('layouts.app')

@section('title', 'Laporan Mahasiswa Magang - Dekan')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Laporan Mahasiswa Magang & Lokasi Industri</h4>
            <p class="mb-0">Pantau secara lengkap siapa saja mahasiswa fakultas yang sedang/telah magang beserta lokasi perusahaan magangnya.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dekan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan Magang</a></li>
        </ol>
    </div>
</div>

<!-- 4 Summary Stat Cards -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-users" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa</p>
                        <h3 class="text-white">{{ $stats['total_interns'] }}</h3>
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
                        <i class="la la-building" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Lokasi Perusahaan</p>
                        <h3 class="text-white">{{ $stats['total_locations'] }}</h3>
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
                        <p class="mb-1">Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_interns'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-dark">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-dark p-3 rounded-circle">
                        <i class="la la-graduation-cap" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Selesai Magang</p>
                        <h3 class="text-white">{{ $stats['completed_interns'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Action Card -->
<div class="card shadow-sm border-0 mb-4 d-print-none">
    <div class="card-body">
        <form method="GET" action="{{ route('dekan.internships') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <select name="study_program_id" class="form-control">
                    <option value="">-- Semua Program Studi --</option>
                    @foreach($studyPrograms as $prodi)
                        <option value="{{ $prodi->id }}" {{ request('study_program_id') == $prodi->id ? 'selected' : '' }}>
                            {{ $prodi->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="industry_id" class="form-control">
                    <option value="">-- Semua Perusahaan Mitra --</option>
                    @foreach($industries as $ind)
                        <option value="{{ $ind->id }}" {{ request('industry_id') == $ind->id ? 'selected' : '' }}>
                            {{ $ind->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-control">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="waiting_dpl" {{ request('status') === 'waiting_dpl' ? 'selected' : '' }}>Menunggu DPL</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Dihentikan</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" name="search" class="form-control" placeholder="Cari Nama/NIM/Mitra..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-primary flex-grow-1"><i class="la la-filter me-1"></i> Filter</button>
                <a href="{{ route('dekan.internships') }}" class="btn btn-outline-secondary" title="Reset"><i class="la la-refresh"></i></a>
                <button type="button" onclick="window.print()" class="btn btn-info text-white" title="Cetak Laporan"><i class="la la-print"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- Report Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
        <h4 class="card-title">Rincian Siapa & Dimana Mahasiswa Magang</h4>
        <span class="text-muted small">Menampilkan {{ $internships->total() }} data magang</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-md table-hover align-middle">
                <thead>
                    <tr>
                        <th><strong>Mahasiswa (Siapa)</strong></th>
                        <th><strong>Tempat Magang (Dimana)</strong></th>
                        <th><strong>DPL Pembimbing</strong></th>
                        <th><strong>Periode &amp; Kontrak</strong></th>
                        <th><strong>Status Magang</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($internships as $item)
                        @php
                            $badge = $item->status_badge;
                            $agreement = $item->agreement;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $item->student->photo_url }}" class="rounded-circle me-3" width="45" height="45" alt="Avatar">
                                    <div>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ $item->student->user->name ?? '-' }}</h6>
                                        <small class="text-muted">NIM: {{ $item->student->nim ?? '-' }}</small><br>
                                        <span class="badge badge-sm badge-light mt-1">{{ $item->student->studyProgram->name ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-weight-bold text-dark">{{ $item->vacancy->industry->name ?? '-' }}</span>
                                <small class="text-primary font-weight-bold"><i class="la la-briefcase me-1"></i>{{ $item->vacancy->title ?? '-' }}</small>
                                @if($item->vacancy->division)
                                    <small class="d-block text-muted">Divisi: {{ $item->vacancy->division }}</small>
                                @endif
                                <small class="d-block text-muted">Spv: {{ $item->vacancy->supervisor->name ?? '-' }}</small>
                            </td>
                            <td>
                                @if($item->dplAssignment && $item->dplAssignment->lecturer)
                                    <span class="d-block font-weight-bold text-dark">{{ $item->dplAssignment->lecturer->user->name ?? '-' }}</span>
                                    <small class="text-muted">NIDN: {{ $item->dplAssignment->lecturer->nidn ?? '-' }}</small>
                                @else
                                    <span class="badge badge-warning">Belum Ada DPL</span>
                                @endif
                            </td>
                            <td>
                                <span class="d-block text-dark small">
                                    {{ $item->start_date ? $item->start_date->format('d/m/Y') : '-' }} - 
                                    {{ $item->end_date ? $item->end_date->format('d/m/Y') : '-' }}
                                </span>
                                @if($agreement)
                                    @php $agBadge = $agreement->status_badge; @endphp
                                    <span class="badge {{ $agBadge['class'] }} mt-1">Kontrak: {{ $agBadge['label'] }}</span>
                                @else
                                    <span class="badge badge-light text-muted mt-1">Belum Ada Kontrak</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="la la-folder-open d-block mb-2" style="font-size: 36px;"></i>
                                Belum ada data mahasiswa magang sesuai kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-print-none">
            {{ $internships->links() }}
        </div>
    </div>
</div>
@endsection
