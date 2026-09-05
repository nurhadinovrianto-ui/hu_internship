@extends('layouts.app')

@section('title', 'Penilaian Kinerja Magang Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Penilaian Kinerja Magang Industri</h4>
            <p class="mb-0">Evaluasi performa praktikal mahasiswa magang serta lihat status &amp; nilai dari Dosen Pembimbing (DPL).</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2 align-items-center">
        <a href="{{ route('industry.assessment-criteria.index') }}" class="btn btn-primary font-weight-bold btn-sm px-3 py-2">
            <i class="la la-sliders me-1"></i> Kelola Kriteria Dinamis Perusahaan
        </a>
    </div>
</div>

<!-- Filter & Pencarian -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form action="{{ route('industry.assessment.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold">Periode Magang</label>
                            <select name="academic_period_id" class="form-control">
                                <option value="">-- Semua Periode Magang --</option>
                                @foreach($academicPeriods as $period)
                                    <option value="{{ $period->id }}" {{ request('academic_period_id') == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label font-weight-bold">Cari Mahasiswa / NIM</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau NIM..." value="{{ request('search') }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label font-weight-bold">Status Industri</label>
                            <select name="status_industry" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="assessed" {{ request('status_industry') === 'assessed' ? 'selected' : '' }}>Sudah Dinilai</option>
                                <option value="pending" {{ request('status_industry') === 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label font-weight-bold">Status DPL</label>
                            <select name="status_dpl" class="form-control">
                                <option value="">-- Semua --</option>
                                <option value="assessed" {{ request('status_dpl') === 'assessed' ? 'selected' : '' }}>Sudah Dinilai</option>
                                <option value="pending" {{ request('status_dpl') === 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 d-flex gap-2">
                            <button type="submit" class="btn btn-primary font-weight-bold w-100">
                                <i class="la la-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'status_industry', 'status_dpl']))
                                <a href="{{ route('industry.assessment.index') }}" class="btn btn-outline-secondary" title="Reset Filter">
                                    <i class="la la-refresh"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa &amp; Prodi</strong></th>
                                <th><strong>Status &amp; Nilai DPL (Kampus)</strong></th>
                                <th><strong>Kriteria Penilaian Dinamis Perusahaan</strong></th>
                                <th><strong>Nilai Akhir Industri</strong></th>
                                <th><strong>Simpan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                @php 
                                    $assess = $intern->assessments->firstWhere('assessor_type', 'industry'); 
                                    $dplAssess = $intern->assessments->firstWhere('assessor_type', 'dpl');
                                @endphp
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted d-block">NIM: {{ $intern->student->nim }}</small>
                                        <span class="badge light bg-secondary text-dark mt-1" style="font-size: 11px;">
                                            {{ $intern->student->studyProgram->name ?? 'Program Studi' }}
                                        </span>
                                    </td>
                                    
                                    <!-- Kolom Status & Nilai dari DPL -->
                                    <td>
                                        @if($dplAssess)
                                            <span class="badge bg-success text-white font-weight-bold px-3 py-1 mb-1 d-inline-block">
                                                <i class="la la-check-circle me-1"></i> Sudah Dinilai DPL
                                            </span>
                                            <div class="font-weight-bold text-dark mt-1" style="font-size: 13.5px;">
                                                Nilai DPL: <span class="text-primary font-weight-bold" style="font-size: 15px;">{{ $dplAssess->final_score }}</span>
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-white font-weight-bold px-3 py-1 mb-1 d-inline-block">
                                                <i class="la la-clock me-1"></i> Belum Dinilai DPL
                                            </span>
                                            <div class="text-muted mt-1" style="font-size: 12.5px;">
                                                Nilai DPL: <span class="text-secondary">-</span>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Form Kriteria Penilaian Dinamis -->
                                    <form action="{{ route('industry.assessment.store', $intern->id) }}" method="POST">
                                        @csrf
                                        
                                        <td>
                                            <div class="row g-2" style="min-width: 400px;">
                                                @foreach($criteria as $criterion)
                                                    @php
                                                        $savedScore = $assess ? $assess->scores->firstWhere('assessment_criterion_id', $criterion->id)?->score : null;
                                                    @endphp
                                                    <div class="col">
                                                        <small class="d-block text-muted text-truncate" style="font-size: 10px; max-width: 95px;" title="{{ $criterion->name }} ({{ $criterion->weight }}%)">
                                                            {{ $criterion->name }} ({{ $criterion->weight }}%)
                                                        </small>
                                                        <input type="number" step="0.01" name="scores[{{ $criterion->id }}]" value="{{ $savedScore ?? $assess?->final_score }}" class="form-control form-control-sm" placeholder="0-100" min="0" max="100" required title="{{ $criterion->name }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td>
                                            @if($assess)
                                                <span class="badge bg-success text-white font-weight-bold py-2 px-3" style="font-size: 14px;">
                                                    {{ $assess->final_score }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assess)
                                                <button type="submit" class="btn btn-warning text-white btn-sm px-3 font-weight-bold" title="Perbarui Nilai">
                                                    <i class="la la-edit me-1"></i> Update
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold" title="Simpan Nilai">
                                                    <i class="la la-save me-1"></i> Simpan
                                                </button>
                                            @endif
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="la la-users d-block mb-3" style="font-size: 48px;"></i>
                                        Belum ada mahasiswa magang yang sesuai dengan filter pencarian Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginasi -->
                @if($internships->hasPages())
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <small class="text-muted">
                            Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari total {{ $internships->total() }} mahasiswa
                        </small>
                        <div>
                            {{ $internships->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
