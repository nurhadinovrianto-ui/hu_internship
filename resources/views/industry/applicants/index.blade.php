@extends('layouts.app')

@section('title', 'Seleksi Pelamar Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Proses Seleksi Pelamar Magang</h4>
            <p class="mb-0">Evaluasi berkas Curriculum Vitae (CV) pelamar, berikan catatan, dan putuskan status penerimaan.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industry.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Seleksi Pelamar</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Kandidat</strong></th>
                                <th><strong>CV &amp; Berkas</strong></th>
                                <th><strong>Pesan Pengantar</strong></th>
                                <th><strong>Catatan Reviewer</strong></th>
                                <th><strong>Tindakan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applicants as $app)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $app->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $app->student->nim }} &bull; GPA: {{ $app->student->gpa }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <a href="{{ asset('storage/' . $app->cv_file) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 13px;">
                                                <i class="la la-file-pdf"></i> Curriculum Vitae (CV)
                                            </a>
                                            @if($app->motivation_letter)
                                                <a href="{{ asset('storage/' . $app->motivation_letter) }}" target="_blank" class="text-primary font-weight-bold" style="font-size: 13px;">
                                                    <i class="la la-file-pdf"></i> Motivation Letter
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="max-width: 250px;">
                                        <span style="font-size: 12.5px;" class="text-muted">{{ Str::limit($app->cover_letter ?? '-', 80) }}</span>
                                    </td>
                                    
                                    <!-- Form Keputusan Seleksi -->
                                    <form action="{{ route('industry.applicants.accept', $app->id) }}" method="POST" id="accept-form-{{ $app->id }}">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <td>
                                            <input type="text" name="notes" id="notes-{{ $app->id }}" class="form-control form-control-sm" placeholder="Catatan penerimaan/penolakan..." required>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button type="submit" class="btn btn-success text-white btn-sm px-3" title="Terima Mahasiswa">
                                                    <i class="la la-check me-1"></i> Terima
                                                </button>
                                                
                                                <button type="button" class="btn btn-outline-danger btn-sm px-3" onclick="rejectApplicant({{ $app->id }})" title="Tolak Mahasiswa">
                                                    <i class="la la-times me-1"></i> Tolak
                                                </button>
                                            </div>
                                        </td>
                                    </form>
                                    
                                    <!-- Reject Action Form -->
                                    <form action="{{ route('industry.applicants.reject', $app->id) }}" method="POST" id="reject-form-{{ $app->id }}" style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="notes" id="reject-notes-{{ $app->id }}">
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pelamar magang yang memerlukan keputusan seleksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $applicants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function rejectApplicant(appId) {
        const notes = document.getElementById('notes-' + appId).value.trim();
        if (!notes) {
            alert('Catatan penolakan wajib diisi pada input Catatan Reviewer sebelum menolak pelamar.');
            return;
        }
        if (confirm('Tolak lamaran mahasiswa ini?')) {
            document.getElementById('reject-notes-' + appId).value = notes;
            document.getElementById('reject-form-' + appId).submit();
        }
    }
</script>
@endsection
