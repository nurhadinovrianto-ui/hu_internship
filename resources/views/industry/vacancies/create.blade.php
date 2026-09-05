@extends('layouts.app')

@section('title', 'Terbitkan Lowongan Baru')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Terbitkan Lowongan Baru</h4>
            <p class="mb-0">Publikasikan posisi lowongan magang baru untuk diisi mahasiswa bertalenta.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industry.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Buat Baru</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                <form action="{{ route('industry.vacancies.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Judul Lowongan Kerja <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Backend Web Developer Intern" value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="position">Nama Posisi / Job Role <span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control" placeholder="Contoh: Software Developer" value="{{ old('position') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="division">Divisi Penempatan</label>
                            <input type="text" name="division" id="division" class="form-control" placeholder="Contoh: Digital Product / IT Division" value="{{ old('division') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="quota">Kuota Praktikan Magang <span class="text-danger">*</span></label>
                            <input type="number" name="quota" id="quota" class="form-control" placeholder="Jumlah kuota..." value="{{ old('quota') }}" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="work_type">Model Kerja <span class="text-danger">*</span></label>
                            <select name="work_type" id="work_type" class="form-control" required>
                                <option value="onsite">Onsite (Ke Kantor)</option>
                                <option value="remote">Remote (Kerja di Rumah)</option>
                                <option value="hybrid">Hybrid (Campuran)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="location">Lokasi Kantor Penempatan</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Contoh: Jakarta Pusat / Bandung" value="{{ old('location') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="duration">Durasi Magang <span class="text-danger">*</span></label>
                            <input type="text" name="duration" id="duration" class="form-control" placeholder="Contoh: 3 Bulan, 1 Semester..." value="{{ old('duration') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="apply_deadline">Batas Akhir Melamar <span class="text-danger">*</span></label>
                            <input type="date" name="apply_deadline" id="apply_deadline" class="form-control" value="{{ old('apply_deadline') }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="description">Deskripsi Pekerjaan / Job Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="requirements">Kualifikasi / Persyaratan Kandidat <span class="text-danger">*</span></label>
                        <textarea name="requirements" id="requirements" class="form-control" rows="5" required>{{ old('requirements') }}</textarea>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('industry.vacancies.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Publikasikan Lowongan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
