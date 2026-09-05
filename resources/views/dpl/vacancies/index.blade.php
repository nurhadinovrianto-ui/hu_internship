@extends('layouts.app')

@section('title', 'Cari Lowongan Magang Mitra')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Cari Lowongan Magang Mitra</h4>
            <p class="mb-0">Jelajahi lowongan magang mitra aktif untuk direkomendasikan kepada mahasiswa bimbingan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Cari Lowongan</a></li>
        </ol>
    </div>
</div>

<!-- Unplaced Mentees Quick Banner -->
@if($unplacedMentees->count() > 0)
    <div class="row mb-3">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="background: rgba(0, 207, 232, 0.08); border-left: 4px solid #00cfe8 !important;">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                <i class="la la-user-clock text-info" style="font-size: 28px;"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 text-dark font-w600">Mahasiswa Bimbingan Anda yang Sedang Mencari Magang ({{ $unplacedMentees->count() }} Orang)</h6>
                                <div class="d-flex gap-2 flex-wrap mt-1">
                                    @foreach($unplacedMentees as $m)
                                        <span class="badge badge-light text-dark font-w500 border">
                                            <i class="la la-user me-1"></i> {{ $m->student?->user?->name }} (NIM: {{ $m->student?->nim }})
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dpl.students', ['status' => 'pre_placement']) }}" class="btn btn-outline-info btn-sm">
                            <i class="la la-list me-1"></i> Lihat Daftar Bimbingan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Search & Filter Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <form action="{{ route('dpl.vacancies.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari posisi pekerjaan, nama perusahaan mitra, keahlian..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="work_type" class="form-control form-select">
                            <option value="">Semua Tipe Kerja</option>
                            <option value="onsite" {{ request('work_type') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="remote" {{ request('work_type') === 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="hybrid" {{ request('work_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-search me-1"></i> Cari
                        </button>
                        @if(request()->anyFilled(['search', 'work_type']))
                            <a href="{{ route('dpl.vacancies.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Vacancies Grid -->
<div class="row">
    @forelse($vacancies as $vac)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100 d-flex flex-column justify-content-between hover-top">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge badge-primary px-2 py-1 font-w500">{{ $vac->work_type_label }}</span>
                        @if($vac->study_program_id)
                            <span class="badge badge-info px-2 py-1">{{ $vac->studyProgram?->code ?? 'Khusus Prodi' }}</span>
                        @else
                            <span class="badge badge-secondary px-2 py-1">Semua Prodi</span>
                        @endif
                    </div>

                    <h5 class="text-dark font-w600 mb-1">{{ $vac->position }}</h5>
                    <p class="text-muted small mb-2">{{ $vac->title }}</p>

                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0" style="width: 38px; height: 38px; border-radius: 8px; background: #F1F5F9; display: flex; align-items: center; justify-content: center;">
                            <i class="la la-building text-primary fs-3"></i>
                        </div>
                        <div class="ms-2">
                            <h6 class="mb-0 text-dark font-w500">{{ $vac->industry->name }}</h6>
                            <small class="text-muted">{{ $vac->location ?? ($vac->industry->city ?? 'Indonesia') }}</small>
                        </div>
                    </div>

                    <p class="text-muted small" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; height: 60px;">
                        {{ Str::limit(strip_tags($vac->description), 140) }}
                    </p>

                    <hr class="my-2">
                    <div class="d-flex justify-content-between align-items-center small text-muted">
                        <span><i class="la la-user-friends me-1"></i> Sisa: <strong class="text-dark">{{ $vac->remaining_quota }}</strong> dari {{ $vac->quota }}</span>
                        <span><i class="la la-clock me-1"></i> Deadline: <strong class="text-dark">{{ $vac->apply_deadline?->format('d M Y') }}</strong></span>
                    </div>
                </div>

                <div class="card-footer bg-white border-0 pt-0 pb-3">
                    <a href="{{ route('dpl.vacancies.show', $vac->id) }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="la la-eye me-1"></i> Lihat Rincian & Rekomendasikan
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 py-5 text-center">
                <div class="card-body text-muted">
                    <i class="la la-briefcase fs-1 d-block mb-2 text-muted"></i>
                    <h5>Tidak ada lowongan yang sesuai kriteria pencarian.</h5>
                    <p class="mb-0">Coba ubah kata kunci atau reset filter pencarian.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="d-flex justify-content-end mt-3">
    {{ $vacancies->links() }}
</div>
@endsection
