@extends('layouts.app')

@section('title', 'Detail Pengguna')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Pengguna</h4>
            <p class="mb-0">Informasi profil lengkap akun dan data akademik/profesional yang terhubung.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- User Avatar & Core info Card -->
    <div class="col-xl-4 col-lg-4 col-md-12 mb-4">
        <div class="card shadow-sm border-0 text-center" style="border-radius: 12px; overflow: hidden;">
            <div class="bg-primary p-4" style="height: 100px;"></div>
            <div class="card-body" style="margin-top: -60px;">
                <img src="{{ $user->avatar_url }}" width="120" height="120" class="rounded-circle border border-4 border-white mb-3" style="object-fit: cover;" alt="">
                <h4 class="text-dark mb-0" style="font-weight: 700;">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <span class="badge light badge-primary px-3 py-2" style="font-weight: 600;">
                    {{ $user->getRoleLabel() }}
                </span>
                
                <div class="border-top pt-4 mt-4 text-start">
                    <h6 class="text-dark mb-3" style="font-weight: 700;">Status Akun</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Status Aktif:</span>
                        <span class="badge {{ $user->status === 'active' ? 'badge-success' : 'badge-danger' }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nomor Telepon:</span>
                        <strong class="text-dark">{{ $user->phone ?? '-' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted font-weight-bold">Akun Dibuat:</span>
                        <strong class="text-dark">{{ $user->created_at->format('d M Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role specific details (Right Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                @if($user->hasRole('mahasiswa') && $user->student)
                    @php $student = $user->student; @endphp
                    <h5 class="text-dark mb-4" style="font-weight: 700;"><i class="la la-user-graduate me-1 text-primary"></i> Data Akademik Mahasiswa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Nomor Induk Mahasiswa (NIM):</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $student->nim }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Program Studi:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $student->studyProgram->name }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Angkatan:</span>
                            <strong class="text-dark" style="font-size: 15px;">Tahun {{ $student->batch ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Semester Aktif:</span>
                            <strong class="text-dark" style="font-size: 15px;">Semester {{ $student->current_semester }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">IPK Kumulatif:</span>
                            <strong class="text-success" style="font-size: 15px;">{{ $student->gpa }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Total SKS Lulus:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $student->total_sks }} SKS</strong>
                        </div>
                    </div>

                @elseif($user->hasRole('dpl') && $user->lecturer)
                    @php $lecturer = $user->lecturer; @endphp
                    <h5 class="text-dark mb-4" style="font-weight: 700;"><i class="la la-chalkboard-teacher me-1 text-primary"></i> Data Profesional Dosen</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Nomor Induk Pegawai (NIP):</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $lecturer->nip ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">NIDN:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $lecturer->nidn ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Homebase Program Studi:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $lecturer->studyProgram->name }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Bidang Keahlian:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $lecturer->specialization ?? '-' }}</strong>
                        </div>
                    </div>

                @elseif($user->hasRole('supervisor-industri') && $user->industrySupervisor)
                    @php $supervisor = $user->industrySupervisor; @endphp
                    <h5 class="text-dark mb-4" style="font-weight: 700;"><i class="la la-building me-1 text-primary"></i> Data Staf Mitra Industri</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Perusahaan / Instansi:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $supervisor->industry->name }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Jabatan Pekerjaan:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $supervisor->position ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted d-block" style="font-size: 11px;">Divisi Penempatan:</span>
                            <strong class="text-dark" style="font-size: 15px;">{{ $supervisor->division ?? '-' }}</strong>
                        </div>
                    </div>

                @else
                    <div class="text-center text-muted py-5">
                        <i class="la la-info-circle d-block mb-3" style="font-size: 48px;"></i>
                        <span>Pengguna ini tidak memiliki profil data tambahan yang terkait dengan perannya.</span>
                    </div>
                @endif

                <div class="border-top pt-4 mt-5 d-flex justify-content-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4">Kembali ke Daftar</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
