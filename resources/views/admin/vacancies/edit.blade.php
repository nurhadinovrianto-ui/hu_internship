@extends('layouts.app')

@section('title', 'Edit Lowongan')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Lowongan</h4>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.vacancies.update', $vacancy->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Pilih Perusahaan / Supervisor <span class="text-danger">*</span></label>
                            <select name="industry_supervisor_id" class="form-control default-select" required>
                                <option value="">-- Pilih Supervisor Industri --</option>
                                @foreach($supervisors as $supervisor)
                                    <option value="{{ $supervisor->id }}" {{ old('industry_supervisor_id', $vacancy->industry_supervisor_id) == $supervisor->id ? 'selected' : '' }}>
                                        {{ $supervisor->industry?->name ?? 'Perusahaan' }} - {{ $supervisor->user?->name ?? '-' }} ({{ $supervisor->position }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Lowongan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $vacancy->title) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Posisi (Role) <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', $vacancy->position) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi (Opsional)</label>
                            <input type="text" name="division" class="form-control" value="{{ old('division', $vacancy->division) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Batas Akhir Melamar <span class="text-danger">*</span></label>
                            <input type="date" name="apply_deadline" class="form-control" value="{{ old('apply_deadline', $vacancy->apply_deadline?->format('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipe Pekerjaan <span class="text-danger">*</span></label>
                            <select name="work_type" class="form-control default-select" required>
                                <option value="onsite" {{ old('work_type', $vacancy->work_type) == 'onsite' ? 'selected' : '' }}>On-Site</option>
                                <option value="remote" {{ old('work_type', $vacancy->work_type) == 'remote' ? 'selected' : '' }}>Remote</option>
                                <option value="hybrid" {{ old('work_type', $vacancy->work_type) == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', $vacancy->location) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Durasi Magang <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control" placeholder="Contoh: 3 Bulan..." value="{{ old('duration', $vacancy->duration) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kuota Mahasiswa <span class="text-danger">*</span></label>
                            <input type="number" name="quota" class="form-control" value="{{ old('quota', $vacancy->quota) }}" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status Lowongan <span class="text-danger">*</span></label>
                            <select name="is_closed" class="form-control default-select">
                                <option value="0" {{ old('is_closed', $vacancy->is_closed ? '1' : '0') === '0' ? 'selected' : '' }}>Dibuka (Menerima Pelamar)</option>
                                <option value="1" {{ old('is_closed', $vacancy->is_closed ? '1' : '0') === '1' ? 'selected' : '' }}>Ditutup (Tidak Menerima Pelamar)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $vacancy->description) }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Persyaratan <span class="text-danger">*</span></label>
                            <textarea name="requirements" class="form-control" rows="4" required>{{ old('requirements', $vacancy->requirements) }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Lowongan</button>
                    <a href="{{ route('admin.vacancies.index') }}" class="btn btn-light">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
