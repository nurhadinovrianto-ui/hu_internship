@extends('layouts.app')

@section('title', 'Edit Program Studi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Program Studi</h4>
            <p class="mb-0">Ubah detail fakultas penempatan, kode prodi, jenjang, dan nama kaprodi.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.study-programs.index') }}">Prodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Ubah Data Program Studi</h5>
                
                <form action="{{ route('admin.study-programs.update', $studyProgram->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-group mb-3">
                        <label class="form-label" for="faculty_id">Fakultas <span class="text-danger">*</span></label>
                        <select name="faculty_id" id="faculty_id" class="form-control" required>
                            @foreach($faculties as $fac)
                                <option value="{{ $fac->id }}" {{ old('faculty_id', $studyProgram->faculty_id) == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="code">Kode Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="code" id="code" class="form-control" value="{{ old('code', $studyProgram->code) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="name">Nama Prodi <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $studyProgram->name) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="degree">Jenjang <span class="text-danger">*</span></label>
                            <select name="degree" id="degree" class="form-control" required>
                                <option value="S1" {{ old('degree', $studyProgram->degree) === 'S1' ? 'selected' : '' }}>S1</option>
                                <option value="D3" {{ old('degree', $studyProgram->degree) === 'D3' ? 'selected' : '' }}>D3</option>
                                <option value="D4" {{ old('degree', $studyProgram->degree) === 'D4' ? 'selected' : '' }}>D4</option>
                                <option value="S2" {{ old('degree', $studyProgram->degree) === 'S2' ? 'selected' : '' }}>S2</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="head_name">Kepala Prodi</label>
                            <input type="text" name="head_name" id="head_name" class="form-control" value="{{ old('head_name', $studyProgram->head_name) }}">
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('admin.study-programs.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
