@extends('layouts.app')

@section('title', 'Manajemen Periode Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Periode Magang</h4>
            <p class="mb-0">Kelola dan aktifkan periode pendaftaran magang akademik untuk mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Periode Magang</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Add Period Form (Left Side) -->
    <div class="col-xl-4 col-lg-4 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Tambah Periode</h5>
                
                <form action="{{ route('admin.periods.store') }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Semester Ganjil 2024/2025" value="{{ old('name') }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="year">Tahun Akademik <span class="text-danger">*</span></label>
                            <input type="text" name="year" id="year" class="form-control" placeholder="2024/2025" value="{{ old('year') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="semester">Semester <span class="text-danger">*</span></label>
                            <select name="semester" id="semester" class="form-control" required>
                                <option value="ganjil">Ganjil</option>
                                <option value="genap">Genap</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date">Mulai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_date">Selesai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="apply_start">Buka Pendaftaran</label>
                            <input type="date" name="apply_start" id="apply_start" class="form-control">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="apply_end">Tutup Pendaftaran</label>
                            <input type="date" name="apply_end" id="apply_end" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" style="font-weight: 600;">Simpan Periode</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Periods Table (Right Side) -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="text-dark mb-0" style="font-weight: 700;">Daftar Periode Akademik</h5>
                    @if(count($periods) > 0)
                        <form action="{{ route('admin.periods.truncate') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA data periode magang? Tindakan ini tidak dapat dibatalkan!');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" title="Kosongkan Semua Data">
                                <i class="la la-trash-o"></i> Kosongkan Data
                            </button>
                        </form>
                    @endif
                </div>
                
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Nama Periode</strong></th>
                                <th><strong>Pelaksanaan Magang</strong></th>
                                <th><strong>Pendaftaran</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periods as $per)
                                <tr>
                                    <td class="text-dark" style="font-weight: 600;">
                                        {{ $per->name }}
                                    </td>
                                    <td>{{ $per->start_date->format('d/m/Y') }} - {{ $per->end_date->format('d/m/Y') }}</td>
                                    <td>
                                        @if($per->apply_start && $per->apply_end)
                                            {{ $per->apply_start->format('d/m/Y') }} - {{ $per->apply_end->format('d/m/Y') }}
                                        @else
                                            <span class="text-muted">Belum Ditentukan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($per->is_active)
                                            <span class="badge badge-success text-white font-weight-bold">Aktif</span>
                                        @else
                                            <span class="badge badge-secondary text-white">Non-aktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            @if(!$per->is_active)
                                                <form action="{{ route('admin.periods.activate', $per->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-xs" title="Aktifkan Periode Ini">
                                                        <i class="la la-check-circle"></i> Aktifkan
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            <a href="{{ route('admin.periods.edit', $per->id) }}" class="btn btn-primary btn-xs" title="Edit Periode">
                                                <i class="la la-edit"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.periods.destroy', $per->id) }}" method="POST" onsubmit="return confirm('Hapus periode ini secara permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus Periode">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada periode akademik terdaftar.</td>
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
