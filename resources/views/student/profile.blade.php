@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Profil Mahasiswa</h4>
            <p class="mb-0">Perbarui data profil pribadi, kontak darurat, dan foto profil Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Profil</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Left Profile Card -->
    <div class="col-xl-4 col-lg-4 col-md-12 mb-4">
        <div class="card shadow-sm border-0 text-center" style="border-radius: 12px; overflow: hidden;">
            <div class="bg-primary p-4" style="height: 100px;"></div>
            <div class="card-body" style="margin-top: -60px;">
                <img src="{{ $student->photo_url }}" width="120" height="120" class="rounded-circle border border-4 border-white mb-3" style="object-fit: cover;" alt="">
                <h4 class="text-dark mb-0" style="font-weight: 700;">{{ $student->user->name }}</h4>
                <p class="text-muted mb-3">NIM: {{ $student->nim }}</p>
                
                <span class="badge light badge-primary px-3 py-2" style="font-weight: 600;">
                    Prodi {{ $student->studyProgram->name }}
                </span>
                
                <div class="border-top pt-4 mt-4 text-start">
                    <h6 class="text-dark mb-3" style="font-weight: 700;">Informasi Akademik</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Semester:</span>
                        <strong class="text-dark">{{ $student->current_semester }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total SKS Lulus:</span>
                        <strong class="text-dark">{{ $student->total_sks }} SKS</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">IPK Kumulatif:</span>
                        <strong class="text-success">{{ $student->gpa }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Profile Edit Form -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Lengkapi Informasi Profil</h5>
                
                <form action="{{ route('student.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $student->user->name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">Nomor Telepon/WA</label>
                            <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $student->user->phone) }}" placeholder="Contoh: 08123456789">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="gender">Jenis Kelamin</label>
                            <select name="gender" id="gender" class="form-control">
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('gender', $student->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date_of_birth">Tanggal Lahir</label>
                            <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" value="{{ old('date_of_birth', $student->date_of_birth ? $student->date_of_birth->toDateString() : '') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="emergency_contact">Kontak Darurat (Orang Tua/Wali)</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" class="form-control" value="{{ old('emergency_contact', $student->emergency_contact) }}" placeholder="Contoh: 08987654321">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="photo">Ganti Foto Profil</label>
                            <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Maksimal resolusi foto 2MB.</small>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="address">Alamat Tinggal Sekarang</label>
                        <textarea name="address" id="address" class="form-control" rows="3" placeholder="Tulis alamat rumah lengkap Anda...">{{ old('address', $student->address) }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-5">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
