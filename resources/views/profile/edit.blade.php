@extends('layouts.app')

@section('title', 'Manajemen Profil & CV')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Profil & Dokumen Saya</h4>
            <p class="mb-0">Kelola identitas diri, dokumen Curriculum Vitae (CV), dan keamanan akun Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Akun</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Profil & Dokumen</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- LEFT SIDEBAR: PROFILE SUMMARY & QUICK INFO -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center">
                <div class="profile-photo mb-3 position-relative d-inline-block">
                    <img src="{{ $user->avatar_url }}" class="rounded-circle shadow-sm" width="130" height="130" alt="Avatar" style="object-fit: cover; border: 4px solid #fff;">
                </div>
                <h4 class="mt-2 mb-1 text-dark" style="font-weight: 700;">{{ $user->name }}</h4>
                <p class="text-muted mb-2 small">{{ $user->email }}</p>
                
                <span class="badge badge-primary px-3 py-1 mb-3" style="font-size: 13px;">
                    <i class="la la-user-shield me-1"></i> {{ strtoupper($user->role) }}
                </span>

                @if($user->phone)
                    <div class="text-muted small mb-2">
                        <i class="la la-phone text-primary me-1"></i> {{ $user->phone }}
                    </div>
                @endif

                <!-- KHUSUS MAHASISWA -->
                @if($user->hasRole('mahasiswa') && $user->student)
                    @php $mhs = $user->student; @endphp
                    <hr class="my-3">
                    <div class="text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">NIM:</span>
                            <span class="font-w600 text-dark small">{{ $mhs->nim }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Program Studi:</span>
                            <span class="font-w600 text-dark small">{{ $mhs->studyProgram?->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Semester / SKS:</span>
                            <span class="font-w600 text-dark small">Sem. {{ $mhs->current_semester }} / {{ $mhs->total_sks }} SKS</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">IPK Akademik:</span>
                            <span class="badge badge-success-subtle text-success font-w600">{{ number_format($mhs->gpa, 2) }}</span>
                        </div>
                    </div>

                    <!-- WIDGET CV MAHASISWA -->
                    <div class="p-3 bg-light rounded mt-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-w600 text-dark small"><i class="la la-file-pdf text-danger me-1"></i> Status CV:</span>
                            @if($mhs->cv_file)
                                <span class="badge badge-success rounded-pill" style="font-size: 11px;">Tersedia</span>
                            @else
                                <span class="badge badge-warning rounded-pill text-white" style="font-size: 11px;">Belum Diunggah</span>
                            @endif
                        </div>
                        @if($mhs->cv_file)
                            <a href="{{ $mhs->cv_url }}" target="_blank" class="btn btn-outline-primary btn-xs w-100 mt-1">
                                <i class="la la-download me-1"></i> Unduh / Preview CV
                            </a>
                        @else
                            <small class="text-muted d-block mt-1">Unggah CV Anda agar dapat langsung melamar magang tanpa upload berulang.</small>
                        @endif
                    </div>

                    <!-- TAUTAN SOSIAL & PORTOFOLIO -->
                    @if($mhs->linkedin_url || $mhs->portfolio_url || $mhs->github_url)
                        <div class="mt-3 pt-3 border-top text-start">
                            <span class="d-block font-w600 text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Tautan Profesional:</span>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($mhs->linkedin_url)
                                    <a href="{{ $mhs->linkedin_url }}" target="_blank" class="btn btn-xs btn-outline-primary" title="LinkedIn">
                                        <i class="la la-linkedin"></i> LinkedIn
                                    </a>
                                @endif
                                @if($mhs->portfolio_url)
                                    <a href="{{ $mhs->portfolio_url }}" target="_blank" class="btn btn-xs btn-outline-info" title="Portofolio">
                                        <i class="la la-globe"></i> Portofolio
                                    </a>
                                @endif
                                @if($mhs->github_url)
                                    <a href="{{ $mhs->github_url }}" target="_blank" class="btn btn-xs btn-outline-dark" title="GitHub">
                                        <i class="la la-github"></i> GitHub
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- TAG SKILLS -->
                    @if(count($mhs->skills_array) > 0)
                        <div class="mt-3 text-start">
                            <span class="d-block font-w600 text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Keahlian Teknis:</span>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($mhs->skills_array as $skill)
                                    <span class="badge badge-light text-dark font-w500 px-2 py-1">{{ $skill }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                <!-- KHUSUS DPL (DOSEN) -->
                @elseif($user->hasRole('dpl') && $user->lecturer)
                    @php $dpl = $user->lecturer; @endphp
                    <hr class="my-3">
                    <div class="text-start">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">NIDN:</span>
                            <span class="font-w600 text-dark small">{{ $dpl->nidn ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">NIP:</span>
                            <span class="font-w600 text-dark small">{{ $dpl->nip ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Jabatan Fungsional:</span>
                            <span class="font-w600 text-dark small">{{ $dpl->position ?? 'Dosen Pengajar' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Program Studi:</span>
                            <span class="font-w600 text-dark small">{{ $dpl->studyProgram?->name ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">Ruang Kerja:</span>
                            <span class="font-w600 text-dark small">{{ $dpl->office_room ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- WIDGET CV DPL -->
                    <div class="p-3 bg-light rounded mt-3 text-start">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-w600 text-dark small"><i class="la la-file-pdf text-danger me-1"></i> CV / Resume:</span>
                            @if($dpl->cv_file)
                                <span class="badge badge-success rounded-pill" style="font-size: 11px;">Tersedia</span>
                            @else
                                <span class="badge badge-warning rounded-pill text-white" style="font-size: 11px;">Belum Diunggah</span>
                            @endif
                        </div>
                        @if($dpl->cv_file)
                            <a href="{{ $dpl->cv_url }}" target="_blank" class="btn btn-outline-primary btn-xs w-100 mt-1">
                                <i class="la la-download me-1"></i> Unduh / Preview CV
                            </a>
                        @endif
                    </div>

                    <!-- TAUTAN RISET & AKADEMIK -->
                    @if($dpl->scholar_url || $dpl->sinta_url || $dpl->linkedin_url)
                        <div class="mt-3 pt-3 border-top text-start">
                            <span class="d-block font-w600 text-muted mb-2 text-uppercase" style="font-size: 0.75rem;">Profil Riset & Akademik:</span>
                            <div class="d-flex gap-2 flex-wrap">
                                @if($dpl->scholar_url)
                                    <a href="{{ $dpl->scholar_url }}" target="_blank" class="btn btn-xs btn-outline-primary" title="Google Scholar">
                                        <i class="la la-graduation-cap"></i> Scholar
                                    </a>
                                @endif
                                @if($dpl->sinta_url)
                                    <a href="{{ $dpl->sinta_url }}" target="_blank" class="btn btn-xs btn-outline-info" title="SINTA">
                                        <i class="la la-book"></i> SINTA
                                    </a>
                                @endif
                                @if($dpl->linkedin_url)
                                    <a href="{{ $dpl->linkedin_url }}" target="_blank" class="btn btn-xs btn-outline-dark" title="LinkedIn">
                                        <i class="la la-linkedin"></i> LinkedIn
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    
    <!-- RIGHT SIDEBAR: EDIT FORMS WITH TABS -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    @if($user->hasRole('mahasiswa') && $user->student)
                        <li class="nav-item">
                            <a class="nav-link active font-w600" data-bs-toggle="tab" href="#student_profile_tab" role="tab">
                                <i class="la la-id-card me-1"></i> Profil Mahasiswa & CV
                            </a>
                        </li>
                    @elseif($user->hasRole('dpl') && $user->lecturer)
                        <li class="nav-item">
                            <a class="nav-link active font-w600" data-bs-toggle="tab" href="#dpl_profile_tab" role="tab">
                                <i class="la la-chalkboard-teacher me-1"></i> Profil DPL & Kepakaran
                            </a>
                        </li>
                    @endif
                    
                    <li class="nav-item">
                        <a class="nav-link {{ (!$user->hasRole('mahasiswa') && !$user->hasRole('dpl')) ? 'active' : '' }} font-w600" data-bs-toggle="tab" href="#account_security_tab" role="tab">
                            <i class="la la-user-lock me-1"></i> Akun & Keamanan
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body pt-4">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="tab-content">
                        <!-- ============================================== -->
                        <!-- TAB 1: FORM SPESIFIK MAHASISWA & CV            -->
                        <!-- ============================================== -->
                        @if($user->hasRole('mahasiswa') && $user->student)
                            @php $mhs = $user->student; @endphp
                            <div class="tab-pane fade show active" id="student_profile_tab" role="tabpanel">
                                
                                <!-- SECTION A: UPLOAD CV & DOKUMEN AKADEMIK -->
                                <div class="p-3 mb-4 rounded" style="background: rgba(115, 103, 240, 0.05); border: 1px dashed #7367f0;">
                                    <h5 class="text-primary font-w600 mb-2">
                                        <i class="la la-file-pdf me-1"></i> Dokumen Curriculum Vitae (CV) & Transkrip
                                    </h5>
                                    <p class="text-muted small mb-3">
                                        Unggah file CV terbaru Anda dalam format PDF. CV ini dapat langsung digunakan saat melamar lowongan magang mitra industri tanpa perlu upload berulang.
                                    </p>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label font-w600 text-dark">
                                                Curriculum Vitae (CV) <span class="text-muted font-weight-normal">(PDF, Maks. 5MB)</span>
                                            </label>
                                            <input type="file" name="cv_file" class="form-control @error('cv_file') is-invalid @enderror" accept=".pdf">
                                            @error('cv_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                            
                                            @if($mhs->cv_file)
                                                <div class="mt-2 small text-success">
                                                    <i class="la la-check-circle me-1"></i> CV tersimpan: 
                                                    <a href="{{ $mhs->cv_url }}" target="_blank" class="fw-bold text-decoration-underline">Preview CV</a>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label font-w600 text-dark">
                                                Transkrip Nilai Sementara <span class="text-muted font-weight-normal">(PDF, Opsional)</span>
                                            </label>
                                            <input type="file" name="transcript_file" class="form-control @error('transcript_file') is-invalid @enderror" accept=".pdf">
                                            @error('transcript_file') <span class="invalid-feedback">{{ $message }}</span> @enderror

                                            @if($mhs->transcript_file)
                                                <div class="mt-2 small text-success">
                                                    <i class="la la-check-circle me-1"></i> Transkrip tersimpan: 
                                                    <a href="{{ $mhs->transcript_url }}" target="_blank" class="fw-bold text-decoration-underline">Preview Transkrip</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- SECTION B: KONTAK & DATA PRIBADI -->
                                <h5 class="text-dark font-w600 mb-3 border-bottom pb-2">Kontak & Data Pribadi</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Nomor WhatsApp / Handphone <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="Contoh: 081234567890" required>
                                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Kontak Darurat (Orang Tua / Wali)</label>
                                        <input type="text" name="emergency_contact" class="form-control @error('emergency_contact') is-invalid @enderror" value="{{ old('emergency_contact', $mhs->emergency_contact) }}" placeholder="Nama & No. HP Orang Tua / Kerabat">
                                        @error('emergency_contact') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Tanggal Lahir</label>
                                        <input type="date" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth', $mhs->date_of_birth?->format('Y-m-d')) }}">
                                        @error('date_of_birth') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Jenis Kelamin</label>
                                        <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                            <option value="">Pilih Jenis Kelamin...</option>
                                            <option value="L" {{ old('gender', $mhs->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                                            <option value="P" {{ old('gender', $mhs->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                                        </select>
                                        @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label font-w600">Alamat Tempat Tinggal / Domisili</label>
                                        <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="2" placeholder="Alamat lengkap tempat tinggal mahasiswa saat ini...">{{ old('address', $mhs->address) }}</textarea>
                                        @error('address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- SECTION C: PORTOFOLIO & KOMPETENSI -->
                                <h5 class="text-dark font-w600 mb-3 border-bottom pb-2">Tautan Portofolio & Kompetensi</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-linkedin text-primary me-1"></i> Profil LinkedIn</label>
                                        <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" value="{{ old('linkedin_url', $mhs->linkedin_url) }}" placeholder="https://linkedin.com/in/username">
                                        @error('linkedin_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-globe text-info me-1"></i> Website Portofolio</label>
                                        <input type="url" name="portfolio_url" class="form-control @error('portfolio_url') is-invalid @enderror" value="{{ old('portfolio_url', $mhs->portfolio_url) }}" placeholder="https://myportfolio.com">
                                        @error('portfolio_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-github text-dark me-1"></i> Link GitHub / Repository</label>
                                        <input type="url" name="github_url" class="form-control @error('github_url') is-invalid @enderror" value="{{ old('github_url', $mhs->github_url) }}" placeholder="https://github.com/username">
                                        @error('github_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label font-w600">Daftar Keahlian Utama (Skills)</label>
                                        <input type="text" name="skills" class="form-control @error('skills') is-invalid @enderror" value="{{ old('skills', $mhs->skills) }}" placeholder="Pisahkan dengan koma, contoh: Laravel, MySQL, UI/UX Figma, React.js, Public Speaking">
                                        <small class="text-muted">Pisahkan setiap keahlian dengan tanda koma (,).</small>
                                        @error('skills') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label font-w600">Ringkasan Diri / Bio Singkat (Elevator Pitch)</label>
                                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3" placeholder="Ceritakan ketertarikan karir, spesialisasi, dan motivasi profesional Anda kepada perusahaan mitra industri...">{{ old('bio', $mhs->bio) }}</textarea>
                                        @error('bio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                        <!-- ============================================== -->
                        <!-- TAB 1 (DPL): FORM SPESIFIK DOSEN & KEPAKARAN   -->
                        <!-- ============================================== -->
                        @elseif($user->hasRole('dpl') && $user->lecturer)
                            @php $dpl = $user->lecturer; @endphp
                            <div class="tab-pane fade show active" id="dpl_profile_tab" role="tabpanel">
                                
                                <h5 class="text-dark font-w600 mb-3 border-bottom pb-2">Identitas Akademik Dosen</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label font-w600">NIDN (Nomor Induk Dosen Nasional)</label>
                                        <input type="text" name="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn', $dpl->nidn) }}" placeholder="Cth: 0412345678">
                                        @error('nidn') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">NIP (Nomor Induk Pegawai)</label>
                                        <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror" value="{{ old('nip', $dpl->nip) }}" placeholder="Cth: 198501012010121001">
                                        @error('nip') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Nomor WhatsApp / HP Dosen <span class="text-danger">*</span></label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="08123456789" required>
                                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Jabatan Fungsional Akademik</label>
                                        <select name="position" class="form-control @error('position') is-invalid @enderror">
                                            <option value="">Pilih Jabatan...</option>
                                            <option value="Tenaga Pengajar" {{ old('position', $dpl->position) === 'Tenaga Pengajar' ? 'selected' : '' }}>Tenaga Pengajar</option>
                                            <option value="Asisten Ahli" {{ old('position', $dpl->position) === 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                            <option value="Lektor" {{ old('position', $dpl->position) === 'Lektor' ? 'selected' : '' }}>Lektor</option>
                                            <option value="Lektor Kepala" {{ old('position', $dpl->position) === 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                            <option value="Guru Besar" {{ old('position', $dpl->position) === 'Guru Besar' ? 'selected' : '' }}>Guru Besar / Profesor</option>
                                        </select>
                                        @error('position') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Bidang Kepakaran / Keahlian</label>
                                        <input type="text" name="specialization" class="form-control @error('specialization') is-invalid @enderror" value="{{ old('specialization', $dpl->specialization) }}" placeholder="Cth: Rekayasa Perangkat Lunak, Data Science, AI">
                                        @error('specialization') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Ruang Kerja / Kantor di Kampus</label>
                                        <input type="text" name="office_room" class="form-control @error('office_room') is-invalid @enderror" value="{{ old('office_room', $dpl->office_room) }}" placeholder="Cth: Gedung Rektorat Lt. 2 Ruang D-204">
                                        @error('office_room') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <h5 class="text-dark font-w600 mb-3 border-bottom pb-2">Tautan Profil Akademik & CV Dosen</h5>
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-graduation-cap text-primary me-1"></i> Google Scholar</label>
                                        <input type="url" name="scholar_url" class="form-control @error('scholar_url') is-invalid @enderror" value="{{ old('scholar_url', $dpl->scholar_url) }}" placeholder="https://scholar.google.com/citations?user=...">
                                        @error('scholar_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-book text-info me-1"></i> SINTA Kemendikbud</label>
                                        <input type="url" name="sinta_url" class="form-control @error('sinta_url') is-invalid @enderror" value="{{ old('sinta_url', $dpl->sinta_url) }}" placeholder="https://sinta.kemdikbud.go.id/authors/profile/...">
                                        @error('sinta_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label font-w600"><i class="la la-linkedin text-dark me-1"></i> LinkedIn</label>
                                        <input type="url" name="linkedin_url" class="form-control @error('linkedin_url') is-invalid @enderror" value="{{ old('linkedin_url', $dpl->linkedin_url) }}" placeholder="https://linkedin.com/in/username">
                                        @error('linkedin_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label font-w600">Dokumen CV / Portofolio Riset Dosen <span class="text-muted font-weight-normal">(PDF, Maks. 5MB)</span></label>
                                        <input type="file" name="cv_file" class="form-control @error('cv_file') is-invalid @enderror" accept=".pdf">
                                        @error('cv_file') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                        @if($dpl->cv_file)
                                            <div class="mt-2 small text-success">
                                                <i class="la la-check-circle me-1"></i> Dokumen tersimpan: 
                                                <a href="{{ $dpl->cv_url }}" target="_blank" class="fw-bold text-decoration-underline">Preview Dokumen</a>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label font-w600">Bio Singkat / Profil Pengalaman Riset & Bimbingan</label>
                                        <textarea name="bio" class="form-control @error('bio') is-invalid @enderror" rows="3" placeholder="Tuliskan fokus penelitian, pengalaman bimbingan, atau pesan untuk mahasiswa bimbingan...">{{ old('bio', $dpl->bio) }}</textarea>
                                        @error('bio') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- ============================================== -->
                        <!-- TAB 2: INFORMASI DASAR AKUN & KEAMANAN         -->
                        <!-- ============================================== -->
                        <div class="tab-pane fade {{ (!$user->hasRole('mahasiswa') && !$user->hasRole('dpl')) ? 'show active' : '' }}" id="account_security_tab" role="tabpanel">
                            <h5 class="text-dark font-w600 mb-3 border-bottom pb-2">Informasi Akun</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label font-w600">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-w600">Alamat Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                
                                @if(!$user->hasRole('mahasiswa') && !$user->hasRole('dpl'))
                                    <div class="col-md-6">
                                        <label class="form-label font-w600">Nomor WhatsApp / Telepon</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="08123456789">
                                        @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                    </div>
                                @endif

                                <div class="col-12">
                                    <label class="form-label font-w600">Ubah Foto Profil Avatar</label>
                                    <input type="file" name="avatar" class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted mt-1 d-block">Format: JPG, PNG, WEBP. Maksimal 2MB.</small>
                                    @error('avatar') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <h5 class="text-dark font-w600 mb-2 border-bottom pb-2">Ubah Kata Sandi (Password)</h5>
                            <p class="text-muted small mb-3">Kosongkan bagian ini jika Anda tidak ingin mengganti kata sandi.</p>

                            <div class="row g-3 mb-4">
                                <div class="col-md-12">
                                    <label class="form-label font-w600">Password Saat Ini</label>
                                    <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="Masukkan password Anda saat ini">
                                    @error('current_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-w600">Password Baru</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password baru minimal 8 karakter">
                                    @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label font-w600">Ulangi Password Baru</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 font-w600 shadow-sm">
                            <i class="la la-save me-1"></i> Simpan Semua Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

