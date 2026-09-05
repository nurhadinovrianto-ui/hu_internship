@extends('layouts.app')

@section('title', 'Manajemen Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Lowongan Magang</h4>
            <p class="mb-0">Kelola semua lowongan magang dari seluruh perusahaan mitra.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Lowongan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4 class="card-title">Daftar Lowongan</h4>
                <a href="{{ route('admin.vacancies.create') }}" class="btn btn-primary btn-sm">
                    <i class="la la-plus"></i> Tambah Lowongan
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.vacancies.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari posisi, judul, perusahaan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="industry_id" class="form-control form-select">
                            <option value="">Semua Perusahaan</option>
                            @foreach($industries as $ind)
                                <option value="{{ $ind->id }}" {{ request('industry_id') == $ind->id ? 'selected' : '' }}>{{ $ind->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="work_type" class="form-control form-select">
                            <option value="">Tipe Kerja</option>
                            <option value="onsite" {{ request('work_type') === 'onsite' ? 'selected' : '' }}>Onsite</option>
                            <option value="remote" {{ request('work_type') === 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="hybrid" {{ request('work_type') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <select name="status" class="form-control form-select">
                            <option value="">Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Dibuka</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Ditutup</option>
                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm px-3" title="Filter">
                            <i class="la la-filter"></i>
                        </button>
                        @if(request()->anyFilled(['search', 'industry_id', 'work_type', 'status']))
                            <a href="{{ route('admin.vacancies.index') }}" class="btn btn-light btn-sm" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>
            
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>Posisi</strong></th>
                                <th><strong>Perusahaan</strong></th>
                                <th><strong>Periode</strong></th>
                                <th><strong>Kuota</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacancies as $vacancy)
                                <tr>
                                    <td>
                                        <strong>{{ $vacancy->position }}</strong><br>
                                        <small class="text-muted">{{ $vacancy->title }}</small>
                                    </td>
                                    <td>
                                        {{ $vacancy->industry->name }}<br>
                                        <small class="text-muted">Oleh: {{ $vacancy->supervisor->user->name ?? '-' }}</small>
                                    </td>
                                    <td>{{ $vacancy->academicPeriod->name ?? '-' }}</td>
                                    <td>{{ $vacancy->applications_count }} / {{ $vacancy->quota }}</td>
                                    <td>
                                        <span class="badge {{ $vacancy->status_badge['class'] }}">
                                            {{ $vacancy->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.vacancies.edit', $vacancy->id) }}" class="btn btn-primary btn-xs shadow">
                                                <i class="la la-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.vacancies.toggle-status', $vacancy->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn {{ $vacancy->is_closed ? 'btn-success' : 'btn-warning' }} btn-xs shadow" title="{{ $vacancy->is_closed ? 'Buka Lowongan' : 'Tutup Lowongan' }}">
                                                    <i class="la {{ $vacancy->is_closed ? 'la-door-open' : 'la-door-closed' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.vacancies.destroy', $vacancy->id) }}" method="POST" onsubmit="return confirm('Hapus lowongan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs shadow">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data lowongan ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-4">
                    <small class="text-muted">
                        Menampilkan {{ $vacancies->firstItem() ?? 0 }} - {{ $vacancies->lastItem() ?? 0 }} dari {{ $vacancies->total() }} lowongan
                    </small>
                    <div>
                        {{ $vacancies->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
