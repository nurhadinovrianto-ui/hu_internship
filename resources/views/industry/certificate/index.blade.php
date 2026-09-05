@extends('layouts.app')

@section('title', 'Penerbitan Sertifikat Magang Industri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Penerbitan Sertifikat Magang Industri</h4>
            <p class="mb-0">Generate sertifikat otomatis dengan desain background perusahaan Anda atau unggah file sertifikat manual.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2 align-items-center">
        <a href="{{ route('industry.certificates.template') }}" class="btn btn-primary font-weight-bold btn-sm px-3 py-2">
            <i class="la la-paint-brush me-1"></i> Atur Desain &amp; Background Sertifikat
        </a>
    </div>
</div>

<!-- Pencarian -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-4">
                <form action="{{ route('industry.certificates.index') }}" method="GET">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label font-weight-bold">Cari Mahasiswa / NIM</label>
                            <input type="text" name="search" class="form-control" placeholder="Nama atau NIM mahasiswa magang..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label font-weight-bold">Status Sertifikat</label>
                            <select name="cert_status" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Status Sertifikat</option>
                                <option value="issued" {{ request('cert_status') === 'issued' ? 'selected' : '' }}>Sudah Diterbitkan</option>
                                <option value="pending" {{ request('cert_status') === 'pending' ? 'selected' : '' }}>Belum Diterbitkan</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-primary font-weight-bold flex-grow-1">
                                <i class="la la-filter me-1"></i> Filter
                            </button>
                            @if(request()->hasAny(['search', 'cert_status']))
                                <a href="{{ route('industry.certificates.index') }}" class="btn btn-light" title="Reset">
                                    <i class="la la-undo"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa &amp; NIM</strong></th>
                                <th><strong>Posisi / Lowongan</strong></th>
                                <th><strong>Nilai Industri</strong></th>
                                <th><strong>Status Sertifikat</strong></th>
                                <th class="text-end"><strong>Aksi Penerbitan / Unduh</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                @php
                                    $assess = $intern->assessments->firstWhere('assessor_type', 'industry');
                                    $cert = $intern->certificate;
                                @endphp
                                <tr>
                                    <td>
                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted d-block">NIM: {{ $intern->student->nim }}</small>
                                        <span class="badge light bg-secondary text-dark mt-1" style="font-size: 11px;">
                                            {{ $intern->student->studyProgram->name ?? 'Program Studi' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-dark d-block">{{ $intern->vacancy->title }}</span>
                                        <small class="text-muted">{{ $intern->vacancy->department ?: 'Magang Industri' }}</small>
                                    </td>
                                    <td>
                                        @if($assess)
                                            <span class="badge bg-success text-white font-weight-bold px-3 py-1" style="font-size: 13px;">
                                                {{ $assess->final_score }}
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-white font-weight-bold px-2 py-1">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($cert)
                                            @if($cert->issuance_type === 'manual_upload')
                                                <span class="badge bg-info text-white px-3 py-1 mb-1 d-inline-block font-weight-bold">
                                                    <i class="la la-upload me-1"></i> Diunggah Manual
                                                </span>
                                            @else
                                                <span class="badge bg-primary text-white px-3 py-1 mb-1 d-inline-block font-weight-bold">
                                                    <i class="la la-magic me-1"></i> Generator Sistem
                                                </span>
                                            @endif
                                            <small class="d-block text-muted mt-1" style="font-size: 11.5px;">
                                                No: {{ $cert->certificate_number }}
                                            </small>
                                        @else
                                            <span class="badge bg-secondary text-white px-3 py-1 font-weight-bold">
                                                Belum Diterbitkan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(!$assess)
                                            <span class="text-muted small">Berikan penilaian industri dahulu</span>
                                        @else
                                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                @if($cert)
                                                    <a href="{{ route('industry.certificates.download', $intern->id) }}" class="btn btn-success text-white btn-sm px-3 font-weight-bold" target="_blank">
                                                        <i class="la la-download me-1"></i> Unduh Sertifikat
                                                    </a>
                                                @endif

                                                <!-- Tombol Generate Otomatis -->
                                                <form action="{{ route('industry.certificates.generate', $intern->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm px-3 font-weight-bold" title="Generate dengan Template Perusahaan">
                                                        <i class="la la-magic me-1"></i> {{ $cert ? 'Regenerate' : 'Generate PDF' }}
                                                    </button>
                                                </form>

                                                <!-- Tombol Modal Upload Manual -->
                                                <button type="button" class="btn btn-outline-primary btn-sm px-3 font-weight-bold" data-bs-toggle="modal" data-bs-target="#uploadModal{{ $intern->id }}">
                                                    <i class="la la-upload me-1"></i> Upload Manual
                                                </button>
                                            </div>

                                            <!-- Modal Upload Manual -->
                                            <div class="modal fade" id="uploadModal{{ $intern->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content text-start">
                                                        <form action="{{ route('industry.certificates.upload', $intern->id) }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-weight-bold">Upload Sertifikat Manual</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-muted small mb-3">
                                                                    Unggah file sertifikat untuk <strong>{{ $intern->student->user->name }}</strong> yang sudah Anda terbitkan dari sistem perusahaan Anda.
                                                                </p>
                                                                <div class="mb-3">
                                                                    <label class="form-label font-weight-bold">Nomor Sertifikat (Opsional)</label>
                                                                    <input type="text" name="certificate_number" class="form-control" value="{{ $cert?->certificate_number }}" placeholder="Nomor sertifikat dari perusahaan Anda">
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label font-weight-bold">File Sertifikat (PDF / JPG / PNG)</label>
                                                                    <input type="file" name="certificate_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                                                                    <small class="text-muted">Maksimal 5MB.</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary font-weight-bold">
                                                                    <i class="la la-upload me-1"></i> Simpan File Sertifikat
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">
                                        <i class="la la-certificate d-block mb-3" style="font-size: 48px;"></i>
                                        Belum ada mahasiswa magang untuk diterbitkan sertifikatnya.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($internships->hasPages() || $internships->total() > 0)
                    <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari {{ $internships->total() }} data</small>
                        {{ $internships->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
