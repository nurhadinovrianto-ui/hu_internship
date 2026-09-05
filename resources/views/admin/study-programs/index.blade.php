@extends('layouts.app')

@section('title', 'Manajemen Program Studi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Program Studi</h4>
            <p class="mb-0">Kelola program studi untuk setiap Fakultas yang terdaftar.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Prodi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Add Study Program (Left Side) -->
    <div class="col-xl-4 col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Tambah Program Studi</h5>
                
                <form action="{{ route('admin.study-programs.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="faculty_id">Fakultas <span class="text-danger">*</span></label>
                        <select name="faculty_id" id="faculty_id" class="form-control" required>
                            <option value="">Pilih Fakultas...</option>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ old('faculty_id') == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="code">Kode Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="Contoh: TI" value="{{ old('code') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Teknik Informatika" value="{{ old('name') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="degree">Jenjang <span class="text-danger">*</span></label>
                            <select name="degree" id="degree" class="form-control" required>
                                <option value="S1">S1</option>
                                <option value="D3">D3</option>
                                <option value="D4">D4</option>
                                <option value="S2">S2</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="head_name">Kepala Prodi</label>
                            <input type="text" name="head_name" id="head_name" class="form-control" placeholder="Nama Kaprodi..." value="{{ old('head_name') }}">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="font-weight: 600;">Simpan Prodi</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Study Programs Table (Right Side) -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Daftar Program Studi</h5>
                
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Kode</strong></th>
                                <th><strong>Nama Prodi</strong></th>
                                <th><strong>Fakultas</strong></th>
                                <th><strong>Kaprodi</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($studyPrograms as $prodi)
                                <tr>
                                    <td><span class="badge light badge-primary" style="font-weight: 700;">{{ $prodi->degree }} - {{ $prodi->code }}</span></td>
                                    <td class="text-dark" style="font-weight: 600;">{{ $prodi->name }}</td>
                                    <td>{{ $prodi->faculty->code }}</td>
                                    <td>{{ $prodi->head_name ?? 'Belum Ditentukan' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.study-programs.destroy', $prodi->id) }}" method="POST" onsubmit="return confirm('Hapus program studi ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus Prodi">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada data program studi.</td>
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
