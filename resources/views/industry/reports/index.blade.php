@extends('layouts.app')

@section('title', 'Laporan Proyek / Software (Industri)')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pemeriksaan Laporan Proyek & Software</h4>
            <p class="mb-0">Daftar laporan progres teknis, pembuatan software, dan luaran magang dari mahasiswa di industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan Proyek/Software</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body">
                <form action="{{ url()->current() }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari judul proyek, mahasiswa, atau NIM..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Status Review</option>
                            <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Menunggu Industri</option>
                            <option value="industry_approved" {{ request('status') === 'industry_approved' ? 'selected' : '' }}>Disetujui Industri</option>
                            <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Perlu Revisi</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1"><i class="la la-filter me-1"></i> Filter</button>
                        @if(request()->hasAny(['search', 'status']))
                            <a href="{{ url()->current() }}" class="btn btn-light" title="Reset"><i class="la la-undo"></i></a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Judul Proyek / Software & Versi</strong></th>
                                <th><strong>Dokumen Laporan</strong></th>
                                <th><strong>Status Review</strong></th>
                                <th><strong>Aksi Review & Riwayat</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $rep)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $rep->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $rep->student->nim }}</small>
                                    </td>
                                    <td>
                                        <div class="font-weight-bold text-dark">{{ Str::limit($rep->title, 55) }}</div>
                                        <small class="badge badge-sm bg-info text-white">Versi {{ $rep->revisions->max('version') ?? 1 }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ asset('storage/' . $rep->file_path) }}" target="_blank" class="badge light badge-primary px-3 py-2 font-weight-bold">
                                            <i class="la la-file-pdf me-1"></i> Lihat PDF
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ str_replace('badge-', 'bg-', $rep->status_badge['class']) }} text-white font-weight-bold">
                                            {{ $rep->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-primary btn-sm px-3 font-weight-bold" type="button" data-bs-toggle="collapse" data-bs-target="#feedbackForm{{ $rep->id }}">
                                                <i class="la la-comment-dots me-1"></i> Review
                                            </button>
                                            @if($rep->revisions && $rep->revisions->count() > 0)
                                                <button class="btn btn-outline-info btn-sm px-3 font-weight-bold" type="button" data-bs-toggle="modal" data-bs-target="#historyModalInd{{ $rep->id }}">
                                                    <i class="la la-history me-1"></i> Riwayat ({{ $rep->revisions->count() }})
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                
                                <tr class="collapse" id="feedbackForm{{ $rep->id }}">
                                    <td colspan="5" class="bg-light p-4">
                                        <form action="{{ route('industry.reports.approve', $rep->id) }}" method="POST">
                                            @csrf
                                            <h6 class="text-dark font-weight-bold mb-3">Evaluasi Laporan Proyek & Pembuatan Software:</h6>
                                            
                                            <div class="row">
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label font-weight-bold">Status Keputusan</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="industry_approved" {{ $rep->status === 'industry_approved' ? 'selected' : '' }}>Setujui Laporan (Approved)</option>
                                                        <option value="revision" {{ $rep->status === 'revision' ? 'selected' : '' }}>Minta Revisi (Revision Required)</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-9 mb-3">
                                                    <label class="form-label font-weight-bold">Catatan / Koreksi Revisi Laporan Proyek</label>
                                                    <textarea name="feedback" class="form-control" rows="3" placeholder="Tulis catatan revisi teknis atau apresiasi progress...">{{ $rep->dpl_feedback }}</textarea>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-end gap-2 mt-3">
                                                <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-toggle="collapse" data-bs-target="#feedbackForm{{ $rep->id }}">Batal</button>
                                                <button type="submit" class="btn btn-success text-white btn-sm px-4 font-weight-bold">Simpan Keputusan Review</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Modal Riwayat Revisi -->
                                <div class="modal fade" id="historyModalInd{{ $rep->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title font-weight-bold">Riwayat Unggahan Laporan Proyek - {{ $rep->student->user->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                @foreach($rep->revisions as $rev)
                                                    <div class="p-3 mb-3 border rounded" style="background-color: #F8FAFC;">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="badge bg-info text-white">Versi {{ $rev->version }}</span>
                                                            <span class="badge {{ str_replace('badge-', 'bg-', $rev->status_badge['class']) }} text-white">{{ $rev->status_badge['label'] }}</span>
                                                        </div>
                                                        <div class="font-weight-bold text-dark mb-1">{{ $rev->title }}</div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <small class="text-muted">{{ $rev->created_at->format('d M Y, H:i') }} WIB</small>
                                                            <a href="{{ asset('storage/' . $rev->file_path) }}" target="_blank" class="text-info font-weight-bold">Lihat File PDF V{{ $rev->version }}</a>
                                                        </div>
                                                        @if($rev->feedback)
                                                            <div class="p-2 mt-2 rounded" style="background-color: #FEF3C7; border-left: 3px solid #F59E0B;">
                                                                <small class="d-block font-weight-bold text-dark">Catatan Supervisor Industri:</small>
                                                                <small class="text-dark">{!! nl2br(e($rev->feedback)) !!}</small>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-5">Belum ada laporan proyek/software yang diunggah mahasiswa magang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($reports->hasPages() || $reports->total() > 0)
                    <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                        <small class="text-muted">Menampilkan {{ $reports->firstItem() ?? 0 }} - {{ $reports->lastItem() ?? 0 }} dari {{ $reports->total() }} data</small>
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
