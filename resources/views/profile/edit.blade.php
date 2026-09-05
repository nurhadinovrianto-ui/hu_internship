@extends('layouts.app')

@section('title', 'Manajemen Profil')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Profil Akun</h4>
            <p class="mb-0">Perbarui informasi dasar dan keamanan akun Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Akun</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Profil</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center">
                <div class="profile-photo mb-3">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle shadow-sm" width="130" height="130" alt="Avatar" style="object-fit: cover; border: 4px solid #fff;">
                </div>
                <h4 class="mt-4 mb-1" style="font-weight: 700;">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                
                <span class="badge badge-primary px-3 py-1 mt-2" style="font-size: 13px;">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
    </div>
    
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Pengaturan Profil</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="col-12 mb-4">
                            <label class="form-label font-weight-bold">Ubah Foto Profil (Opsional)</label>
                            <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                            @error('avatar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted mt-1 d-block">Maksimal ukuran file: 2MB. Format: JPG, PNG, JPEG.</small>
                        </div>
                        
                        <div class="col-12 mt-4 pt-4 border-top">
                            <h5 class="mb-3 text-dark font-weight-bold">Keamanan (Ubah Password)</h5>
                            <p class="text-muted small mb-4">Kosongkan kolom di bawah ini jika Anda tidak ingin mengubah password.</p>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label font-weight-bold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password Anda saat ini">
                            @error('current_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password baru">
                            @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                        </div>
                        
                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="la la-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
