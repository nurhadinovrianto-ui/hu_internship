@extends('layouts.app')

@section('title', 'Edit Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Lowongan Magang</h4>
            <p class="mb-0">Perbarui informasi dan persyaratan kualifikasi posisi lowongan magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industry.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit Lowongan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                <form action="{{ route('industry.vacancies.update', $vacancy->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Judul Lowongan Kerja <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Backend Web Developer Intern" value="{{ old('title', $vacancy->title) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="position">Nama Posisi / Job Role <span class="text-danger">*</span></label>
                            <input type="text" name="position" id="position" class="form-control" placeholder="Contoh: Software Developer" value="{{ old('position', $vacancy->position) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="division">Divisi Penempatan</label>
                            <input type="text" name="division" id="division" class="form-control" placeholder="Contoh: Digital Product / IT Division" value="{{ old('division', $vacancy->division) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="quota">Kuota Praktikan Magang <span class="text-danger">*</span></label>
                            <input type="number" name="quota" id="quota" class="form-control" placeholder="Jumlah kuota..." value="{{ old('quota', $vacancy->quota) }}" min="1" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="work_type">Model Kerja <span class="text-danger">*</span></label>
                            <select name="work_type" id="work_type" class="form-control" required>
                                <option value="onsite" {{ old('work_type', $vacancy->work_type) === 'onsite' ? 'selected' : '' }}>Onsite (Ke Kantor)</option>
                                <option value="remote" {{ old('work_type', $vacancy->work_type) === 'remote' ? 'selected' : '' }}>Remote (Kerja di Rumah)</option>
                                <option value="hybrid" {{ old('work_type', $vacancy->work_type) === 'hybrid' ? 'selected' : '' }}>Hybrid (Campuran)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="location">Lokasi Kantor Penempatan</label>
                            <input type="text" name="location" id="location" class="form-control" placeholder="Contoh: Jakarta Pusat / Bandung" value="{{ old('location', $vacancy->location) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="duration">Durasi Magang <span class="text-danger">*</span></label>
                            <input type="text" name="duration" id="duration" class="form-control" placeholder="Contoh: 3 Bulan, 1 Semester..." value="{{ old('duration', $vacancy->duration) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="apply_deadline">Batas Akhir Melamar <span class="text-danger">*</span></label>
                            <input type="date" name="apply_deadline" id="apply_deadline" class="form-control" value="{{ old('apply_deadline', $vacancy->apply_deadline?->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label" for="is_closed">Status Lowongan <span class="text-danger">*</span></label>
                            <select name="is_closed" id="is_closed" class="form-control">
                                <option value="0" {{ old('is_closed', $vacancy->is_closed ? '1' : '0') === '0' ? 'selected' : '' }}>Dibuka (Menerima Pelamar)</option>
                                <option value="1" {{ old('is_closed', $vacancy->is_closed ? '1' : '0') === '1' ? 'selected' : '' }}>Ditutup (Tidak Menerima Pelamar)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="description">Deskripsi Pekerjaan / Job Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="5" required>{{ old('description', $vacancy->description) }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="requirements">Kualifikasi / Persyaratan Kandidat <span class="text-danger">*</span></label>
                        <textarea name="requirements" id="requirements" class="form-control" rows="5" required>{{ old('requirements', $vacancy->requirements) }}</textarea>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mt-4">
                        <a href="{{ route('industry.vacancies.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
