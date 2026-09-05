@extends('layouts.app')

@section('title', 'Validasi Pengajuan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Review Pengajuan Magang</h4>
            <p class="mb-0">Evaluasi profil akademik, berkas lamaran, dan setujui/tolak pengajuan mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.applications.index') }}">Validasi Pengajuan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Review</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Student Profile & Files (Left Side) -->
    <div class="col-xl-8 col-lg-8 col-md-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-5">
                <div class="d-flex align-items-center mb-4 border-bottom pb-4">
                    <img src="{{ $application->student->photo_url }}" width="65" height="65" class="rounded-circle border border-4 border-light me-3" style="object-fit: cover;" alt="">
                    <div>
                        <h4 class="text-dark mb-0" style="font-weight: 700;">{{ $application->student->user->name }}</h4>
                        <p class="text-muted mb-0">NIM: {{ $application->student->nim }} &bull; Angkatan: {{ $application->student->batch }}</p>
                    </div>
                </div>

                <h5 class="text-dark mb-3" style="font-weight: 700;">Detail Akademik</h5>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted d-block" style="font-size: 11px;">IPK Kumulatif:</span>
                            <strong class="text-success" style="font-size: 18px;">{{ $application->student->gpa }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted d-block" style="font-size: 11px;">SKS yang Lulus:</span>
                            <strong class="text-dark" style="font-size: 18px;">{{ $application->student->total_sks }} SKS</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="text-muted d-block" style="font-size: 11px;">Semester Aktif:</span>
                            <strong class="text-dark" style="font-size: 18px;">{{ $application->student->current_semester }}</strong>
                        </div>
                    </div>
                </div>

                <h5 class="text-dark mb-3" style="font-weight: 700;">Berkas Terlampir</h5>
                <div class="d-flex gap-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="la la-file-pdf text-danger me-2" style="font-size: 28px;"></i>
                        <a href="{{ asset('storage/' . $application->cv_file) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 14px;">Curriculum Vitae (CV)</a>
                    </div>
                    
                    @if($application->motivation_letter)
                        <div class="d-flex align-items-center mb-2">
                            <i class="la la-file-pdf text-danger me-2" style="font-size: 28px;"></i>
                            <a href="{{ asset('storage/' . $application->motivation_letter) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 14px;">Motivation Letter</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Approval Action Form (Right Side) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="card-body p-4">
                <h5 class="text-dark mb-4" style="font-weight: 700;">Tindakan Validasi Akademik</h5>

                @if($application->status === 'pending')
                    <form action="{{ route('kaprodi.applications.approve', $application->id) }}" method="POST" id="approve-form">
                        @csrf
                        @method('PATCH')
                        
                        <div class="form-group mb-4">
                            <label class="form-label" for="notes">Catatan Rekomendasi Kaprodi</label>
                            <textarea name="notes" id="notes" class="form-control" rows="4" placeholder="Tulis catatan persetujuan atau revisi akademik jika diperlukan..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success text-white btn-block mb-2" style="font-weight: 600;">
                            <i class="la la-check-circle me-1"></i> Setujui Pengajuan
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('kaprodi.applications.reject', $application->id) }}" method="POST" id="reject-form" onsubmit="return confirm('Tolak pengajuan magang ini?');">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="notes" id="reject-notes">
                        <button type="button" class="btn btn-outline-danger btn-block" style="font-weight: 600;" onclick="submitReject();">
                            <i class="la la-times-circle me-1"></i> Tolak Pengajuan
                        </button>
                    </form>
                @else
                    <div class="alert alert-info border-0 text-center py-4 mb-0" style="background-color: #F0F9FF; color: #0369A1;">
                        <i class="la la-info-circle" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                        <h5>Sudah Diproses</h5>
                        <p class="mb-0 mt-2" style="font-size: 13px;">Status: 
                            <span class="badge {{ $application->status_badge['class'] }}">
                                {{ $application->status_badge['label'] }}
                            </span>
                        </p>
                        @if(in_array($application->status, ['kaprodi_approved', 'industry_accepted']))
                            <a href="{{ route('kaprodi.applications.letter', $application->id) }}" class="btn btn-sm btn-primary mt-3">
                                <i class="la la-download me-1"></i> Unduh Surat Pengantar
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function submitReject() {
        const notesVal = document.getElementById('notes').value.trim();
        if (!notesVal) {
            alert('Catatan penolakan wajib diisi pada textarea sebelum menolak.');
            return;
        }
        document.getElementById('reject-notes').value = notesVal;
        document.getElementById('reject-form').submit();
    }
</script>
@endsection
