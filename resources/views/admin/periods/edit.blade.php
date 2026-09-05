@extends('layouts.app')

@section('title', 'Edit Periode Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Periode Magang</h4>
            <p class="mb-0">Ubah rentang waktu magang serta batas penyerahan lamaran mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.periods.index') }}">Periode Magang</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Ubah Data Periode Akademik</h5>
                
                <form action="{{ route('admin.periods.update', $period->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Periode <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $period->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="start_date">Mulai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date', $period->start_date ? $period->start_date->toDateString() : '') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="end_date">Selesai Magang <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date', $period->end_date ? $period->end_date->toDateString() : '') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="apply_start">Buka Pendaftaran</label>
                            <input type="date" name="apply_start" id="apply_start" class="form-control" value="{{ old('apply_start', $period->apply_start ? $period->apply_start->toDateString() : '') }}">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="apply_end">Tutup Pendaftaran</label>
                            <input type="date" name="apply_end" id="apply_end" class="form-control" value="{{ old('apply_end', $period->apply_end ? $period->apply_end->toDateString() : '') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('admin.periods.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
