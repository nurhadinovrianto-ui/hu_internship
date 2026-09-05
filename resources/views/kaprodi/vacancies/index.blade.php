@extends('layouts.app')

@section('title', 'Kelola Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kelola Lowongan Magang</h4>
            <p class="mb-0">Unggah dan kelola lowongan magang kemitraan Program Studi {{ $prodi?->name ?? '' }}.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Lowongan</a></li>
        </ol>
    </div>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(115, 103, 240, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-briefcase text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Total Lowongan</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['total'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(40, 199, 111, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-check-circle text-success" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Sedang Dibuka</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['active'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(234, 84, 85, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-times-circle text-danger" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Ditutup / Penuh</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['closed'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255, 159, 67, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-user-graduate text-warning" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.75rem; font-weight: 600;">Total Pelamar</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['total_applicants'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="card-title mb-0">Daftar Lowongan Prodi</h4>
                    <p class="text-muted mb-0 small">Lowongan yang diunggah khusus untuk mahasiswa prodi Anda maupun jejaring mitra.</p>
                </div>
                <a href="{{ route('kaprodi.vacancies.create') }}" class="btn btn-primary btn-sm">
                    <i class="la la-plus me-1"></i> Upload Lowongan Baru
                </a>
            </div>

            <div class="card-body pt-3">
                <!-- Search & Filters -->
                <form action="{{ route('kaprodi.vacancies.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari posisi, judul, nama mitra industri..." value="{{ request('search') }}">
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
                    <div class="col-md-2">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Dibuka</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'work_type', 'status']))
                            <a href="{{ route('kaprodi.vacancies.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Posisi & Judul</strong></th>
                                <th><strong>Mitra Perusahaan</strong></th>
                                <th><strong>Target Prodi</strong></th>
                                <th><strong>Kuota / Pelamar</strong></th>
                                <th><strong>Deadline</strong></th>
                                <th><strong>Status</strong></th>
                                <th class="text-end"><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacancies as $vacancy)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark font-w600">{{ $vacancy->position }}</h6>
                                        <small class="text-muted">{{ $vacancy->title }} &bull; <span class="badge badge-xs badge-light">{{ $vacancy->work_type_label }}</span></small>
                                    </td>
                                    <td>
                                        <span class="text-dark font-w500">{{ $vacancy->industry->name }}</span>
                                        <small class="d-block text-muted">{{ $vacancy->location ?? ($vacancy->industry->city ?? '-') }}</small>
                                    </td>
                                    <td>
                                        @if($vacancy->study_program_id)
                                            <span class="badge badge-info">{{ $vacancy->studyProgram?->code ?? 'Khusus Prodi' }}</span>
                                        @else
                                            <span class="badge badge-secondary">Terbuka Umum</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <span class="font-w600 text-dark">{{ $vacancy->accepted_count }} / {{ $vacancy->quota }}</span>
                                            <small class="text-muted">terisi</small>
                                        </div>
                                        <small class="text-primary font-w500">{{ $vacancy->applications_count }} pelamar</small>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $vacancy->apply_deadline?->format('d M Y') }}</span>
                                        @if($vacancy->apply_deadline < now()->toDateString())
                                            <small class="d-block text-danger">Kadaluarsa</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $vacancy->status_badge['class'] }}">
                                            {{ $vacancy->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <!-- Lihat Pelamar -->
                                            <a href="{{ route('kaprodi.vacancies.applicants', $vacancy->id) }}" class="btn btn-primary btn-sm px-2" title="Lihat & Seleksi Pelamar">
                                                <i class="la la-users me-1"></i> Pelamar ({{ $vacancy->applications_count }})
                                            </a>

                                            <!-- Edit -->
                                            <a href="{{ route('kaprodi.vacancies.edit', $vacancy->id) }}" class="btn btn-outline-info btn-sm px-2" title="Edit Lowongan">
                                                <i class="la la-pencil"></i>
                                            </a>

                                            <!-- Toggle Status (Tutup/Buka) -->
                                            <form action="{{ route('kaprodi.vacancies.toggle-status', $vacancy->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-{{ $vacancy->is_closed ? 'success' : 'warning' }} btn-sm px-2" title="{{ $vacancy->is_closed ? 'Buka Lowongan' : 'Tutup Lowongan' }}">
                                                    <i class="la la-{{ $vacancy->is_closed ? 'unlock' : 'lock' }}"></i>
                                                </button>
                                            </form>

                                            <!-- Delete (jika belum ada pelamar) -->
                                            @if($vacancy->applications_count == 0)
                                                <form action="{{ route('kaprodi.vacancies.destroy', $vacancy->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm px-2" title="Hapus">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="la la-briefcase text-muted fs-1 d-block mb-2"></i>
                                        Belum ada lowongan magang yang diunggah oleh Kaprodi.<br>
                                        <a href="{{ route('kaprodi.vacancies.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="la la-plus me-1"></i> Upload Lowongan Pertama
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $vacancies->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
