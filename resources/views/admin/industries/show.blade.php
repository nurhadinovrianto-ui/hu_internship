@extends('layouts.app')

@section('title', 'Detail Mitra Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Kemitraan Industri</h4>
            <p class="mb-0">Profil lengkap perusahaan, daftar lowongan aktif, dan staf supervisor terdaftar.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.industries.index') }}">Mitra</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Company Profile Card (Left Side) -->
    <div class="col-xl-4 col-lg-4 col-md-12 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="bg-primary p-4 text-center text-white" style="height: 120px; display: flex; align-items: center; justify-content: center;">
                <h5 class="text-white mb-0" style="font-weight: 700; letter-spacing: 0.5px;">{{ strtoupper($industry->partnership_status) }} Mitra</h5>
            </div>
            <div class="card-body text-center" style="margin-top: -50px;">
                <img src="{{ $industry->logo_url }}" width="100" height="100" class="rounded-circle border border-4 border-white mb-3" style="object-fit: cover;" alt="">
                <h4 class="text-dark mb-1" style="font-weight: 700;">{{ $industry->name }}</h4>
                <p class="text-muted mb-3">{{ $industry->industry_type }}</p>

                <div class="border-top pt-4 mt-4 text-start">
                    <h6 class="text-dark mb-3" style="font-weight: 700;">Kontak Kantor</h6>
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 11px;">Alamat Email:</span>
                        <strong class="text-dark" style="font-size: 13.5px;">{{ $industry->email }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 11px;">No Telepon:</span>
                        <strong class="text-dark" style="font-size: 13.5px;">{{ $industry->phone }}</strong>
                    </div>
                    <div class="mb-2">
                        <span class="text-muted d-block" style="font-size: 11px;">Website:</span>
                        <strong class="text-dark" style="font-size: 13.5px;">{{ $industry->website ?? '-' }}</strong>
                    </div>
                    <div class="mb-0">
                        <span class="text-muted d-block" style="font-size: 11px;">Kota / Provinsi:</span>
                        <strong class="text-dark" style="font-size: 13.5px;">{{ $industry->city }}, {{ $industry->province ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vacancies & Supervisors (Right Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <!-- Lowongan Magang -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title" style="font-weight: 700;">Lowongan Magang Diterbitkan</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Posisi Lowongan</strong></th>
                                <th><strong>Kuota</strong></th>
                                <th><strong>Pendaftar</strong></th>
                                <th><strong>Deadline</strong></th>
                                <th><strong>Status</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($industry->vacancies as $vac)
                                <tr>
                                    <td class="text-dark font-weight-bold">{{ $vac->title }}</td>
                                    <td>{{ $vac->quota }} Orang</td>
                                    <td>{{ $vac->applications->count() }} Pelamar</td>
                                    <td>{{ $vac->apply_deadline->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $vac->status_badge['class'] }}">
                                            {{ $vac->status_badge['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada lowongan magang yang diterbitkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Supervisor Terdaftar -->
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title" style="font-weight: 700;">Staf Supervisor Industri</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($industry->supervisors as $sup)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div class="d-flex align-items-center">
                                <img src="{{ $sup->user->avatar_url }}" width="35" height="35" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                <div>
                                    <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $sup->user->name }}</h6>
                                    <small class="text-muted">{{ $sup->user->email }}</small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-info text-white px-3 py-1">{{ $sup->position ?? 'Supervisor' }} &bull; {{ $sup->division ?? 'Divisi' }}</span>
                                
                                @if(auth()->user()->canImpersonate() && $sup->user->canBeImpersonated())
                                    <a href="{{ route('impersonate', $sup->user->id) }}" class="btn btn-dark btn-sm" title="Login Sebagai Supervisor Ini">
                                        <i class="la la-user-secret me-1"></i> Login Sbg Akun Ini
                                    </a>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted px-0">Belum ada supervisor industri yang didaftarkan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
