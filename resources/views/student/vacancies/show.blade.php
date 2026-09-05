@extends('layouts.app')

@section('title', 'Detail Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Lowongan Magang</h4>
            <p class="mb-0">Periksa persyaratan teknis dan profil industri sebelum mengajukan lamaran.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.vacancies.browse') }}">Cari Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Vacancy & Industry Info -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4 border-bottom pb-4">
                    <img src="{{ $vacancy->industry->logo_url }}" width="60" height="60" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                    <div>
                        <h4 class="text-dark mb-0" style="font-weight: 700;">{{ $vacancy->title }}</h4>
                        <p class="text-muted mb-0">{{ $vacancy->industry->name }} &bull; {{ $vacancy->vacancy_type_label ?? $vacancy->industry->industry_type }}</p>
                    </div>
                </div>

                <h5 class="text-dark mb-3" style="font-weight: 700;">Deskripsi Pekerjaan</h5>
                <p class="text-muted leading-relaxed" style="font-size: 14px;">
                    {!! nl2br(e($vacancy->description)) !!}
                </p>

                <h5 class="text-dark mt-5 mb-3" style="font-weight: 700;">Persyaratan Kandidat</h5>
                <p class="text-muted leading-relaxed" style="font-size: 14px;">
                    {!! nl2br(e($vacancy->requirements)) !!}
                </p>
            </div>
        </div>
    </div>

    <!-- Application Form (Right Side) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <!-- Quick Stats -->
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background-color: #6366F1; height: max-content;">
            <div class="card-body p-4 text-white">
                <h5 class="text-white mb-4" style="font-weight: 700;">Informasi Tambahan</h5>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Model Kerja:</span>
                        <strong class="text-white">{{ $vacancy->work_type_label }}</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Lokasi:</span>
                        <strong class="text-white">{{ $vacancy->location ?? 'Remote' }}</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Durasi:</span>
                        <strong class="text-white">{{ $vacancy->duration }}</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-3 border-bottom border-white border-opacity-10 pb-2">
                        <span>Sisa Kuota:</span>
                        <strong class="text-white">{{ $vacancy->remaining_quota }} Orang</strong>
                    </li>
                    <li class="d-flex justify-content-between mb-0">
                        <span>Batas Lamaran:</span>
                        <strong class="text-white">{{ $vacancy->apply_deadline->format('d M Y') }}</strong>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Submit Application Form -->
        <div class="card shadow-sm border-0" style="border-radius: 12px; height: max-content;">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Kirim Lamaran Magang</h5>
                
                <form action="{{ route('student.vacancies.apply', $vacancy->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    @php $profileCv = auth()->user()->student?->cv_file; @endphp

                    @if($profileCv)
                        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center justify-content-between rounded">
                            <div>
                                <i class="la la-check-circle me-1"></i> <span class="fw-bold">CV Profil Tersedia</span>
                                <a href="{{ asset('storage/' . $profileCv) }}" target="_blank" class="ms-1 text-primary text-decoration-underline fw-bold small">Lihat CV</a>
                            </div>
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="use_profile_cv" id="use_profile_cv" value="1" checked onchange="document.getElementById('cv_upload_box').style.display = this.checked ? 'none' : 'block'; document.getElementById('cv_file').required = !this.checked;">
                                <label class="form-check-label small font-w600" for="use_profile_cv">Pakai CV ini</label>
                            </div>
                        </div>
                    @endif

                    <div class="form-group mb-3" id="cv_upload_box" style="{{ $profileCv ? 'display: none;' : '' }}">
                        <label class="form-label" for="cv_file">Curriculum Vitae (CV) {{ $profileCv ? 'Khusus (Opsional jika pakai CV profil)' : '' }} <span class="text-danger">*</span></label>
                        <input type="file" name="cv_file" id="cv_file" class="form-control" accept=".pdf" {{ $profileCv ? '' : 'required' }}>
                        <small class="text-muted">Hanya file PDF dengan ukuran maksimal 2MB.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="motivation_letter">Motivation Letter (Opsional)</label>
                        <input type="file" name="motivation_letter" id="motivation_letter" class="form-control" accept=".pdf">
                        <small class="text-muted">Hanya file PDF dengan ukuran maksimal 2MB.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="cover_letter">Pesan / Surat Pengantar (Opsional)</label>
                        <textarea name="cover_letter" id="cover_letter" class="form-control" rows="4" placeholder="Tulis pengantar singkat Anda..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block py-3" style="font-weight: 600;">Kirim Lamaran Magang</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
