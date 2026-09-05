@extends('layouts.app')

@section('title', 'Daftar Program Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Program Magang</h4>
            <p class="mb-0">Daftar seluruh program magang mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Program Magang</a></li>
        </ol>
    </div>
</div>

<!-- 4 EDUMIN SIGNATURE STAT CARDS -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-users" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['total'] ?? $internships->total() }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Terdaftar Magang</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-user-clock" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Menunggu DPL</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['waiting_dpl'] ?? 0 }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Belum Diplot DPL</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-check-circle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Sedang Aktif</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['active'] ?? 0 }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Aktif di Industri</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-graduation-cap" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Selesai Magang</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['completed'] ?? 0 }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Selesai Program</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Data Mahasiswa Magang Prodi {{ $prodi?->name }}</h4>
                <a href="{{ route('kaprodi.tracking.index') }}" class="btn btn-primary btn-sm">
                    <i class="la la-map-marked-alt me-1"></i> Lacak Mahasiswa Realtime
                </a>
            </div>
            <div class="card-body">
                <!-- Search/Filters -->
                <form action="{{ route('kaprodi.internships.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari mahasiswa, NIM, industri, atau DPL..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Magang</option>
                            <option value="waiting_dpl" {{ request('status') === 'waiting_dpl' ? 'selected' : '' }}>Menunggu DPL</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Diberhentikan / Batal</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('kaprodi.internships.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Lowongan Magang</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>DPL</strong></th>
                                <th><strong>Mulai Magang</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $intern->student->nim }}</small>
                                    </td>
                                    <td>{{ $intern->vacancy->title }}</td>
                                    <td>{{ $intern->vacancy->industry->name }}</td>
                                    <td>
                                        @if($intern->dplAssignment)
                                            <div>
                                                <span class="text-dark font-w600 d-block">{{ $intern->dplAssignment->lecturer->user->name }}</span>
                                                <small class="text-muted">NIDN: {{ $intern->dplAssignment->lecturer->nidn ?? '-' }}</small>
                                            </div>
                                        @else
                                            <span class="badge badge-warning-subtle text-warning">Belum diplot</span>
                                        @endif
                                    </td>
                                    <td>{{ $intern->start_date ? \Carbon\Carbon::parse($intern->start_date)->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="badge {{ $intern->status_badge['class'] }}">
                                            {{ $intern->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($intern->status === 'active' || $intern->status === 'waiting_dpl')
                                            <div class="d-flex align-items-center gap-1">
                                                @if($intern->dplAssignment)
                                                    <button type="button" class="btn btn-warning btn-sm text-white px-2" data-bs-toggle="modal" data-bs-target="#changeDplModal-{{ $intern->id }}" title="Ubah Dosen Pembimbing Lapangan">
                                                        <i class="la la-user-edit me-1"></i> Ubah DPL
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-primary btn-sm px-2" data-bs-toggle="modal" data-bs-target="#changeDplModal-{{ $intern->id }}" title="Plot Dosen Pembimbing Lapangan">
                                                        <i class="la la-user-plus me-1"></i> Plot DPL
                                                    </button>
                                                @endif

                                                <button type="button" class="btn btn-outline-danger btn-sm px-2" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $intern->id }}" title="Batalkan Magang">
                                                    <i class="la la-times-circle"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Modal Ubah / Plot DPL -->
                                            <div class="modal fade" id="changeDplModal-{{ $intern->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <div class="modal-header bg-warning-subtle text-dark">
                                                            <h5 class="modal-title font-w600">
                                                                <i class="la la-user-edit me-1 text-warning"></i> {{ $intern->dplAssignment ? 'Ubah' : 'Tugaskan' }} Dosen Pembimbing (DPL)
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('kaprodi.dpl-plotting.reassign', $intern->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body text-start">
                                                                <div class="p-3 bg-light rounded mb-3">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Mahasiswa</small>
                                                                            <strong class="text-dark">{{ $intern->student->user->name }}</strong>
                                                                            <div class="text-muted small">NIM: {{ $intern->student->nim }}</div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Mitra Industri</small>
                                                                            <strong class="text-dark">{{ $intern->vacancy->industry->name }}</strong>
                                                                            <div class="text-muted small">{{ $intern->vacancy->title }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <hr class="my-2">
                                                                    <div>
                                                                        <small class="text-muted d-block">DPL Saat Ini</small>
                                                                        <span class="badge badge-outline-primary mt-1">
                                                                            <i class="la la-user me-1"></i>
                                                                            {{ $intern->dplAssignment?->lecturer?->user?->name ?? 'Belum Ditugaskan' }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label font-w600 text-dark">
                                                                        Pilih Dosen Pembimbing (DPL) {{ $intern->dplAssignment ? 'Baru' : '' }} <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="lecturer_id" class="form-control" required>
                                                                        <option value="">-- Pilih Dosen DPL --</option>
                                                                        @foreach($lecturers as $lec)
                                                                            @php
                                                                                $isCurrent = $intern->dplAssignment && $intern->dplAssignment->lecturer_id == $lec['id'];
                                                                            @endphp
                                                                            <option value="{{ $lec['id'] }}" 
                                                                                {{ $isCurrent ? 'disabled' : '' }}
                                                                                {{ !$lec['has_capacity'] && !$isCurrent ? 'disabled' : '' }}>
                                                                                {{ $lec['name'] }} 
                                                                                (Beban: {{ $lec['current_mentee'] }}/{{ $lec['max_mentee'] }})
                                                                                {{ $isCurrent ? ' - [DPL Saat Ini]' : '' }}
                                                                                {{ !$lec['has_capacity'] && !$isCurrent ? ' - [Kuota Penuh]' : '' }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label font-w600 text-dark">
                                                                        Alasan / Catatan Perubahan <span class="text-muted font-weight-normal">(Opsional)</span>
                                                                    </label>
                                                                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Penyesuaian bidang keahlian magang, rotasi beban bimbingan dosen..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning btn-sm text-white px-3">
                                                                    <i class="la la-check me-1"></i> Simpan DPL
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelModal-{{ $intern->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title">Berhentikan / Batalkan Magang</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('kaprodi.internships.cancel', $intern->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body text-start">
                                                                <p>Anda yakin ingin memberhentikan magang mahasiswa <strong>{{ $intern->student->user->name }}</strong>?</p>
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Cth: Mengundurkan diri, pelanggaran berat, dll"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-danger">Konfirmasi Pemberhentian</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada program magang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari {{ $internships->total() }} magang
                    </small>
                    <div>
                        {{ $internships->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
