@extends('layouts.app')

@section('title', 'Edit Fakultas')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Fakultas</h4>
            <p class="mb-0">Ubah kode fakultas, nama, dan nama dekan yang menjabat.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.faculties.index') }}">Fakultas</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Ubah Data Fakultas</h5>
                
                <form action="{{ route('admin.faculties.update', $faculty->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="code">Kode Fakultas <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $faculty->code) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Fakultas <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $faculty->name) }}" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="dean_name">Nama Dekan</label>
                        <input type="text" name="dean_name" id="dean_name" class="form-control" value="{{ old('dean_name', $faculty->dean_name) }}">
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
