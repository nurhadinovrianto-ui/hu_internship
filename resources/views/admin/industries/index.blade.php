@extends('layouts.app')

@section('title', 'Manajemen Mitra Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kemitraan Industri</h4>
            <p class="mb-0">Daftar instansi, perusahaan, dan BUMN mitra program magang mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Mitra Industri</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Mitra Industri Aktif</h4>
                <a href="{{ route('admin.industries.create') }}" class="btn btn-primary btn-sm px-4">
                    <i class="la la-plus me-1"></i> Tambah Mitra Baru
                </a>
            </div>
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('admin.industries.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama perusahaan, kota, bidang..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="partnership_status" class="form-control form-select">
                            <option value="">Semua Kerjasama</option>
                            <option value="mou" {{ request('partnership_status') === 'mou' ? 'selected' : '' }}>MOU (Aktif)</option>
                            <option value="moa" {{ request('partnership_status') === 'moa' ? 'selected' : '' }}>MOA</option>
                            <option value="none" {{ request('partnership_status') === 'none' ? 'selected' : '' }}>Tanpa Kerjasama</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="is_partner" class="form-control form-select">
                            <option value="">Status Mitra</option>
                            <option value="1" {{ request('is_partner') === '1' ? 'selected' : '' }}>Mitra Resmi</option>
                            <option value="0" {{ request('is_partner') === '0' ? 'selected' : '' }}>Bukan Mitra</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'partnership_status', 'is_partner']))
                            <a href="{{ route('admin.industries.index') }}" class="btn btn-light" title="Reset Filter">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Bidang / Type</strong></th>
                                <th><strong>Lokasi</strong></th>
                                <th><strong>Kerjasama (MOU)</strong></th>
                                <th><strong>Status Mitra</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($industries as $ind)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $ind->logo_url }}" width="35" height="35" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <div>
                                                <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $ind->name }}</h6>
                                                <small class="text-muted">{{ $ind->website ?? $ind->email }}</small>
                                                @if($ind->supervisors->isNotEmpty() && $ind->supervisors->first()->user)
                                                    <div class="mt-1">
                                                        <span class="badge badge-light text-muted" style="font-size: 11px;" title="Akun: {{ $ind->supervisors->first()->user->email }}">
                                                            <i class="la la-user-shield me-1 text-primary"></i>{{ $ind->supervisors->first()->user->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $ind->industry_type }}</td>
                                    <td>{{ $ind->city }}</td>
                                    <td>
                                        <span class="badge {{ $ind->partnership_status !== 'none' ? 'badge-success' : 'badge-light' }}">
                                            {{ strtoupper($ind->partnership_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ind->is_active ? 'badge-success' : 'badge-danger' }}">
                                            {{ $ind->is_active ? 'Aktif' : 'Non-aktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if($ind->supervisors->isNotEmpty() && auth()->user()->canImpersonate() && $ind->supervisors->first()->user?->canBeImpersonated())
                                                <!-- Login As Supervisor -->
                                                <a href="{{ route('impersonate', $ind->supervisors->first()->user->id) }}" class="btn btn-dark btn-xs" title="Login Sebagai Supervisor ({{ $ind->supervisors->first()->user->name }})">
                                                    <i class="la la-user-secret"></i>
                                                </a>
                                            @endif

                                            <!-- Toggle status -->
                                            <form action="{{ route('admin.industries.toggle-partner', $ind->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-xs" title="Ubah Status Kemitraan">
                                                    <i class="la la-power-off"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Show detail -->
                                            <a href="{{ route('admin.industries.show', $ind->id) }}" class="btn btn-info btn-xs" title="Detail Kemitraan">
                                                <i class="la la-eye"></i>
                                            </a>

                                            <!-- Edit -->
                                            <a href="{{ route('admin.industries.edit', $ind->id) }}" class="btn btn-primary btn-xs" title="Edit Data Mitra">
                                                <i class="la la-pencil-alt"></i>
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.industries.destroy', $ind->id) }}" method="POST" onsubmit="return confirm('Hapus mitra ini dari sistem secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus Mitra">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada mitra industri terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $industries->firstItem() ?? 0 }} - {{ $industries->lastItem() ?? 0 }} dari {{ $industries->total() }} industri
                    </small>
                    <div>
                        {{ $industries->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
