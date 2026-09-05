@extends('layouts.app')

@section('title', 'Upload Lowongan Magang Baru')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Upload Lowongan Magang Baru</h4>
            <p class="mb-0">Publikasikan lowongan magang dari jejaring mitra atau prodi untuk mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Upload Baru</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('kaprodi.vacancies.store') }}" method="POST">
                    @csrf

                    <h5 class="text-primary font-w600 mb-3"><i class="la la-building me-1"></i> Informasi Mitra & Sasaran</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Mitra Perusahaan / Instansi <span class="text-danger">*</span></label>
                            <select name="industry_id" class="form-control form-select" required>
                                <option value="">-- Pilih Mitra Perusahaan --</option>
                                @foreach($industries as $ind)
                                    <option value="{{ $ind->id }}" {{ old('industry_id') == $ind->id ? 'selected' : '' }}>
                                        {{ $ind->name }} ({{ $ind->city ?? 'Nasional' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Supervisor Industri (Opsional)</label>
                            <select name="industry_supervisor_id" class="form-control form-select">
                                <option value="">-- Tanpa Supervisor Akun (Dikelola Prodi / Mitra Konvensional) --</option>
                                @foreach($supervisors as $sup)
                                    <option value="{{ $sup->id }}" {{ old('industry_supervisor_id') == $sup->id ? 'selected' : '' }}>
                                        {{ $sup->user->name }} &bull; {{ $sup->industry->name }} ({{ $sup->position }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Bisa dikosongkan jika kemitraan langsung lewat Kaprodi / belum ada akun supervisor.</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label font-w600">Target Mahasiswa Pelamar <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_scope" id="scope_prodi" value="prodi" {{ old('target_scope', 'prodi') === 'prodi' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="scope_prodi">
                                        <strong>Khusus Program Studi {{ $prodi?->name }}</strong> (Hanya mahasiswa prodi Anda yang dapat melamar)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="target_scope" id="scope_all" value="all" {{ old('target_scope') === 'all' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="scope_all">
                                        <strong>Terbuka untuk Semua Program Studi</strong> (Lintas prodi)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="text-primary font-w600 mb-3"><i class="la la-briefcase me-1"></i> Rincian Posisi Magang</h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Judul Lowongan <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" placeholder="Contoh: Internship Junior Backend Developer 2026" value="{{ old('title') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Posisi (Role) <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control" placeholder="Contoh: Backend Developer" value="{{ old('position') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Divisi / Departemen</label>
                            <input type="text" name="division" class="form-control" placeholder="Contoh: IT Engineering" value="{{ old('division') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Kuota Mahasiswa <span class="text-danger">*</span></label>
                            <input type="number" name="quota" class="form-control" min="1" value="{{ old('quota', 2) }}" required>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Durasi Magang <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control" placeholder="Contoh: 6 Bulan" value="{{ old('duration', '6 Bulan') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Batas Akhir Melamar (Deadline) <span class="text-danger">*</span></label>
                            <input type="date" name="apply_deadline" class="form-control" value="{{ old('apply_deadline') }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Tipe Kerja <span class="text-danger">*</span></label>
                            <select name="work_type" class="form-control form-select" required>
                                <option value="onsite" {{ old('work_type') == 'onsite' ? 'selected' : '' }}>Onsite (Di Kantor)</option>
                                <option value="remote" {{ old('work_type') == 'remote' ? 'selected' : '' }}>Remote (WFH)</option>
                                <option value="hybrid" {{ old('work_type') == 'hybrid' ? 'selected' : '' }}>Hybrid (Kombinasi)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Lokasi Kerja</label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Jakarta Selatan / Karawang" value="{{ old('location') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Deskripsi Pekerjaan & Tanggung Jawab <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan ruang lingkup pekerjaan, proyek yang akan dikerjakan, dll." required>{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Persyaratan & Kriteria Pelamar <span class="text-danger">*</span></label>
                            <textarea name="requirements" class="form-control" rows="4" placeholder="Tuliskan skill yang dibutuhkan, bahasa pemrograman, dll." required>{{ old('requirements') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('kaprodi.vacancies.index') }}" class="btn btn-light">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-save me-1"></i> Publikasikan Lowongan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
