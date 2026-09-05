@extends('layouts.app')

@section('title', 'Pelamar Lowongan - ' . $vacancy->position)

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Daftar Pelamar Lowongan</h4>
            <p class="mb-0">{{ $vacancy->position }} &bull; {{ $vacancy->industry->name }}</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.vacancies.index') }}">Lowongan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Pelamar</a></li>
        </ol>
    </div>
</div>

<!-- Vacancy Summary Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="background: linear-gradient(135deg, #4B49AC 0%, #7978E9 100%); color: #fff;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div>
                        <span class="badge bg-white text-primary mb-2 px-3 py-1 font-w600">{{ $vacancy->work_type_label }}</span>
                        <h3 class="text-white mb-1 font-w600">{{ $vacancy->title }}</h3>
                        <p class="mb-0 text-white-50"><i class="la la-building me-1"></i> {{ $vacancy->industry->name }} &bull; <i class="la la-map-marker me-1"></i> {{ $vacancy->location ?? ($vacancy->industry->city ?? '-') }}</p>
                    </div>
                    <div class="d-flex gap-4">
                        <div class="text-center">
                            <span class="d-block text-white-50 small">KUOTA</span>
                            <h4 class="text-white mb-0 font-w600">{{ $vacancy->quota }}</h4>
                        </div>
                        <div class="text-center">
                            <span class="d-block text-white-50 small">TERISI</span>
                            <h4 class="text-white mb-0 font-w600">{{ $vacancy->accepted_count }}</h4>
                        </div>
                        <div class="text-center">
                            <span class="d-block text-white-50 small">SISA KUOTA</span>
                            <h4 class="text-white mb-0 font-w600">{{ $vacancy->remaining_quota }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Semua Pelamar ({{ $applicants->total() }})</h4>
                <a href="{{ route('kaprodi.vacancies.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="la la-arrow-left me-1"></i> Kembali ke Lowongan
                </a>
            </div>

            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Program Studi / SKS</strong></th>
                                <th><strong>Berkas Pelamar</strong></th>
                                <th><strong>Tanggal Melamar</strong></th>
                                <th><strong>Status Lamaran</strong></th>
                                <th><strong>DPL Bimbingan</strong></th>
                                <th class="text-end"><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applicants as $app)
                                @php
                                    $student = $app->student;
                                    $assignedDpl = $student->getDplForPeriod($vacancy->academic_period_id);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student->photo_url }}" width="40" height="40" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <div>
                                                <h6 class="mb-0 text-dark font-w600">{{ $student->user->name }}</h6>
                                                <small class="text-muted">NIM: {{ $student->nim }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark font-w500">{{ $student->studyProgram?->name ?? '-' }}</span>
                                        <small class="d-block text-muted">IPK: {{ $student->gpa ?? '-' }} &bull; {{ $student->total_sks ?? 0 }} SKS</small>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            @if($student->cv_file)
                                                <a href="{{ $student->cv_url }}" target="_blank" class="btn btn-outline-primary btn-xs" title="Lihat CV">
                                                    <i class="la la-file-pdf"></i> CV
                                                </a>
                                            @endif
                                            @if($student->transcript_file)
                                                <a href="{{ $student->transcript_url }}" target="_blank" class="btn btn-outline-info btn-xs" title="Transkrip Nilai">
                                                    <i class="la la-file-alt"></i> Transkrip
                                                </a>
                                            @endif
                                            @if($student->portfolio_url)
                                                <a href="{{ $student->portfolio_url }}" target="_blank" class="btn btn-outline-secondary btn-xs" title="Portfolio">
                                                    <i class="la la-globe"></i> Portfolio
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $app->created_at->format('d M Y') }}</span>
                                        <small class="d-block text-muted">{{ $app->created_at->format('H:i') }} WIB</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $app->status_badge['class'] }}">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assignedDpl)
                                            <span class="badge badge-success px-2 py-1">
                                                <i class="la la-user-check me-1"></i> {{ $assignedDpl->user->name }}
                                            </span>
                                        @else
                                            <span class="badge badge-light text-muted">Belum ada DPL</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if(in_array($app->status, [\App\Models\Application::STATUS_PENDING, \App\Models\Application::STATUS_KAPRODI_APPROVED]))
                                            <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#acceptModal{{ $app->id }}">
                                                <i class="la la-check-circle me-1"></i> Terima Magang
                                            </button>

                                            <!-- Modal Terima Magang -->
                                            <div class="modal fade text-start" id="acceptModal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <form action="{{ route('kaprodi.vacancies.applicants.accept', $app->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-header">
                                                                <h5 class="modal-title font-w600"><i class="la la-check-circle text-success me-1"></i> Konfirmasi Penerimaan Magang</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="mb-2">Anda akan menerima <strong>{{ $student->user->name }}</strong> untuk posisi <strong>{{ $vacancy->position }}</strong> di <strong>{{ $vacancy->industry->name }}</strong>.</p>
                                                                
                                                                @if($assignedDpl)
                                                                    <div class="alert alert-success d-flex align-items-center py-2 px-3 mb-3">
                                                                        <i class="la la-info-circle fs-4 me-2"></i>
                                                                        <small>Mahasiswa ini sudah memiliki DPL bimbingan pra-penempatan: <strong>{{ $assignedDpl->user->name }}</strong>. DPL akan langsung tersambung dan status magang langsung <strong>Aktif</strong>!</small>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-warning d-flex align-items-center py-2 px-3 mb-3">
                                                                        <i class="la la-exclamation-triangle fs-4 me-2"></i>
                                                                        <small>Mahasiswa ini belum memiliki DPL. Setelah diterima, Anda dapat menugaskan DPL melalui menu <strong>Plotting DPL</strong>.</small>
                                                                    </div>
                                                                @endif

                                                                <div class="mb-3">
                                                                    <label class="form-label">Catatan Penerimaan (Opsional)</label>
                                                                    <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Diterima magang berdasarkan konfirmasi pihak mitra HRD."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-success px-3">
                                                                    <i class="la la-check me-1"></i> Konfirmasi & Terima
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small"><i class="la la-check"></i> Selesai Diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="la la-users text-muted fs-1 d-block mb-2"></i>
                                        Belum ada mahasiswa yang melamar pada lowongan ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $applicants->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
