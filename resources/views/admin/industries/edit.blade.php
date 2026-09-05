@extends('layouts.app')

@section('title', 'Edit Mitra Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Mitra Industri</h4>
            <p class="mb-0">Ubah informasi kemitraan, alamat, dan data MOU industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.industries.index') }}">Mitra</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.industries.update', $industry->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">Nama Instansi / Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $industry->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="industry_type">Bidang Industri <span class="text-danger">*</span></label>
                            <input type="text" name="industry_type" id="industry_type" class="form-control" value="{{ old('industry_type', $industry->industry_type) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $industry->email) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">Nomor Telp Kantor <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $industry->phone) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="city">Kota <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $industry->city) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="partnership_status">Tingkat Kerjasama <span class="text-danger">*</span></label>
                            <select name="partnership_status" id="partnership_status" class="form-control" required>
                                <option value="none" {{ old('partnership_status', $industry->partnership_status) === 'none' ? 'selected' : '' }}>Tanpa MOU (Mitra Umum)</option>
                                <option value="mou" {{ old('partnership_status', $industry->partnership_status) === 'mou' ? 'selected' : '' }}>MOU (Memorandum of Understanding)</option>
                                <option value="moa" {{ old('partnership_status', $industry->partnership_status) === 'moa' ? 'selected' : '' }}>MOA (Memorandum of Agreement)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="address">Alamat Lengkap Perusahaan <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address', $industry->address) }}</textarea>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('admin.industries.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
