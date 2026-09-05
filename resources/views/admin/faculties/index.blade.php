@extends('layouts.app')

@section('title', 'Manajemen Fakultas')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Fakultas</h4>
            <p class="mb-0">Kelola daftar Fakultas di Horizon University Indonesia.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Fakultas</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Add Faculty Form (Left Side) -->
    <div class="col-xl-4 col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Tambah Fakultas</h5>
                
                <form action="{{ route('admin.faculties.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="code">Kode Fakultas <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: FICT" value="{{ old('code') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Fakultas <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Fakultas Ilmu Komputer" value="{{ old('name') }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="dean_name">Nama Dekan</label>
                        <input type="text" name="dean_name" id="dean_name" class="form-control" placeholder="Nama Dekan beserta gelar..." value="{{ old('dean_name') }}">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="font-weight: 600;">Simpan Fakultas</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Faculties List Table (Right Side) -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Daftar Fakultas</h5>
                
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Kode</strong></th>
                                <th><strong>Nama Fakultas</strong></th>
                                <th><strong>Dekan</strong></th>
                                <th><strong>Prodi</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($faculties as $fac)
                                <tr>
                                    <td><span class="badge light badge-primary" style="font-weight: 700;">{{ $fac->code }}</span></td>
                                    <td class="text-dark" style="font-weight: 600;">{{ $fac->name }}</td>
                                    <td>{{ $fac->dean_name ?? 'Belum Ditentukan' }}</td>
                                    <td>{{ $fac->study_programs_count }} Prodi</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.faculties.destroy', $fac->id) }}" method="POST" onsubmit="return confirm('Hapus fakultas ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus Fakultas">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data fakultas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
