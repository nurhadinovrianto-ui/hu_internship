@extends('layouts.app')

@section('title', 'Cari Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Peluang Magang Industri</h4>
            <p class="mb-0">Daftar lowongan magang aktif yang tersedia untuk Program Studi Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Cari Lowongan</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-shield-alt text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Akses Pendaftaran Ditangguhkan</h3>
                    <p class="text-muted mx-auto" style="max-width: 600px; font-size: 15px; line-height: 1.6;">
                        {{ $reason }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 mt-3">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body py-4">
                    <form action="{{ route('student.vacancies.browse') }}" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white" style="border-right: none;"><i class="la la-search text-muted"></i></span>
                                <input type="text" name="search" class="form-control ps-0" placeholder="Cari posisi magang, bidang atau nama perusahaan mitra..." value="{{ request('search') }}" style="border-left: none;">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="work_type" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Tipe Kerja</option>
                                <option value="onsite" {{ request('work_type') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                                <option value="remote" {{ request('work_type') === 'remote' ? 'selected' : '' }}>Remote</option>
                                <option value="hybrid" {{ request('work_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1"><i class="la la-filter me-1"></i> Filter</button>
                            @if(request()->hasAny(['search', 'work_type']))
                                <a href="{{ route('student.vacancies.browse') }}" class="btn btn-light" title="Reset"><i class="la la-undo"></i></a>
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
            <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; overflow: hidden; background: #fff; transition: all 0.2s;">
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <div class="d-flex align-items-center mb-3">
                            <img src="{{ $vac->industry->logo_url }}" width="45" height="45" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                            <div>
                                <h6 class="mb-0 text-dark" style="font-weight: 700;">{{ Str::limit($vac->industry->name, 22) }}</h6>
                                <small class="text-muted">{{ $vac->industry->industry_type }}</small>
                            </div>
                        </div>
                        
                        <h5 class="text-dark my-2" style="font-weight: 700;">{{ $vac->title }}</h5>
                        <p class="text-muted flex-grow-1" style="font-size: 13px; line-height: 1.6;">
                            {{ Str::limit(strip_tags($vac->description), 120) }}
                        </p>
                        
                        <div class="d-flex flex-wrap gap-2 my-3">
                            <span class="badge badge-outline-primary" style="font-size: 11px;">
                                <i class="la la-map-marker-alt me-1"></i> {{ $vac->work_type_label }} ({{ $vac->location ?? 'Remote' }})
                            </span>
                            <span class="badge badge-outline-info" style="font-size: 11px;">
                                <i class="la la-hourglass-half me-1"></i> {{ $vac->duration }}
                            </span>
                            <span class="badge badge-outline-success" style="font-size: 11px;">
                                <i class="la la-user-friends me-1"></i> Sisa Kuota: {{ $vac->remaining_quota }}
                            </span>
                        </div>
                        
                        <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                            <span class="text-muted" style="font-size: 11px;">Deadline: {{ $vac->apply_deadline->format('d M Y') }}</span>
                            <a href="{{ route('student.vacancies.show', $vac->id) }}" class="btn btn-primary btn-sm px-3">Detail &amp; Apply</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center p-5">
                        <i class="la la-search-minus text-muted mb-3" style="font-size: 56px;"></i>
                        <h4 class="text-dark" style="font-weight: 700;">Tidak Menemukan Lowongan</h4>
                        <p class="text-muted">Lowongan magang untuk kata kunci tersebut tidak ditemukan atau kuota periode ini sudah ditutup.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($vacancies->hasPages() || $vacancies->total() > 0)
        <div class="row">
            <div class="col-12 mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <small class="text-muted">Menampilkan {{ $vacancies->firstItem() ?? 0 }} - {{ $vacancies->lastItem() ?? 0 }} dari {{ $vacancies->total() }} data</small>
                {{ $vacancies->links() }}
            </div>
        </div>
    @endif
@endif
@endsection
