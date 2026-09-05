@extends('layouts.app')

@section('title', 'Plotting & Pengelolaan DPL')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Plotting & Pengelolaan Dosen Pembimbing (DPL)</h4>
            <p class="mb-0">Tugaskan DPL sebelum atau sesudah mahasiswa ditempatkan magang untuk Program Studi {{ $prodi?->name ?? '' }}.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Plotting DPL</a></li>
        </ol>
    </div>
</div>

<!-- Stat Cards -->
<div class="row">
    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(115, 103, 240, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-user-tag text-primary" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Pra-Penempatan</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['pre_placement_total'] }}</h3>
                        <small class="text-{{ $stats['pre_placement_unassigned'] > 0 ? 'warning' : 'success' }} font-w500">
                            {{ $stats['pre_placement_unassigned'] }} belum ada DPL
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(255, 171, 0, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-user-clock text-warning" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Diterima (Menunggu DPL)</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['waiting'] }}</h3>
                        <small class="text-muted">Siap ditempatkan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(40, 199, 111, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-user-check text-success" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Sedang Magang Aktif</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['assigned'] }}</h3>
                        <small class="text-success font-w500">Berjalan di industri</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0" style="width: 50px; height: 50px; border-radius: 12px; background: rgba(0, 207, 232, 0.12); display: inline-flex; align-items: center; justify-content: center;">
                        <i class="la la-chalkboard-teacher text-info" style="font-size: 24px;"></i>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <span class="d-block text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.5px; font-weight: 600;">Dosen DPL (Beban)</span>
                        <h3 class="mb-0 font-w600 text-dark">{{ $stats['total_lecturers'] }} <span style="font-size: 0.85rem; font-weight: normal;" class="text-muted">dosen</span></h3>
                        <small class="text-info font-w500">Rerata: {{ $stats['avg_mentee'] }} mhs/dosen</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs card-header-tabs" style="border-bottom: none;">
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'pre_placement' ? 'active font-w600' : '' }}" href="{{ route('kaprodi.dpl-plotting.index', ['tab' => 'pre_placement']) }}">
                            <i class="la la-user-tag me-1"></i> Pra-Penempatan (Belum Ditempatkan)
                            <span class="badge badge-primary rounded-pill ms-1">{{ $stats['pre_placement_total'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'waiting' ? 'active font-w600' : '' }}" href="{{ route('kaprodi.dpl-plotting.index', ['tab' => 'waiting']) }}">
                            <i class="la la-user-clock me-1"></i> Diterima Magang (Menunggu DPL)
                            <span class="badge badge-warning rounded-pill ms-1">{{ $stats['waiting'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $tab === 'assigned' ? 'active font-w600' : '' }}" href="{{ route('kaprodi.dpl-plotting.index', ['tab' => 'assigned']) }}">
                            <i class="la la-user-check me-1"></i> Sedang Magang Aktif (Ubah DPL)
                            <span class="badge badge-success rounded-pill ms-1">{{ $stats['assigned'] }}</span>
                        </a>
                    </li>
                </ul>

                <div>
                    <a href="{{ route('kaprodi.internships.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="la la-list me-1"></i> Semua Program Magang
                    </a>
                </div>
            </div>

            <div class="card-body pt-3">
                <!-- Search & Filter Form -->
                <form action="{{ route('kaprodi.dpl-plotting.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    
                    <div class="{{ $tab === 'pre_placement' ? 'col-md-6' : 'col-md-9' }}">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mahasiswa, NIM, industri, atau nama DPL..." value="{{ request('search') }}">
                        </div>
                    </div>

                    @if($tab === 'pre_placement')
                        <div class="col-md-3">
                            <select name="pre_status" class="form-control form-select">
                                <option value="">Semua Status Plotting</option>
                                <option value="unassigned" {{ request('pre_status') === 'unassigned' ? 'selected' : '' }}>Belum Memiliki DPL ({{ $stats['pre_placement_unassigned'] }})</option>
                                <option value="assigned" {{ request('pre_status') === 'assigned' ? 'selected' : '' }}>Sudah Memiliki DPL ({{ $stats['pre_placement_assigned'] }})</option>
                            </select>
                        </div>
                    @endif

                    <div class="{{ $tab === 'pre_placement' ? 'col-md-3' : 'col-md-3' }} d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-search me-1"></i> Cari
                        </button>
                        @if(request()->anyFilled(['search', 'pre_status']))
                            <a href="{{ route('kaprodi.dpl-plotting.index', ['tab' => $tab]) }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                @if($tab === 'pre_placement')
                    <!-- ========================================== -->
                    <!-- TAB 1: PRA-PENEMPATAN (BELUM DITEMPATKAN) -->
                    <!-- ========================================== -->
                    <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center">
                        <i class="la la-info-circle fs-3 me-2"></i>
                        <div>
                            <strong>Fitur Pra-Penempatan:</strong> Anda dapat menetapkan Dosen DPL kepada mahasiswa <em>sebelum</em> mahasiswa mendapatkan tempat magang. DPL yang ditugaskan dapat membimbing dan membantu mencarikan tempat magang. Ketika mahasiswa resmi diterima magang nanti, DPL ini otomatis terhubung!
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Mahasiswa</strong></th>
                                    <th><strong>Akademik</strong></th>
                                    <th><strong>Status Lamaran</strong></th>
                                    <th><strong>DPL Ditugaskan</strong></th>
                                    <th><strong>Plotting DPL</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $std)
                                    @php
                                        $preAssign = $std->dplAssignments->first();
                                        $currentLec = $preAssign?->lecturer;
                                        $activeAppsCount = $std->applications->whereIn('status', ['pending', 'kaprodi_approved'])->count();
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $std->photo_url }}" width="38" height="38" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                                <div>
                                                    <h6 class="mb-0 text-dark font-w600">{{ $std->user->name }}</h6>
                                                    <small class="text-muted">NIM: {{ $std->nim }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark font-w500">IPK: {{ $std->gpa ?? '-' }}</span>
                                            <small class="d-block text-muted">{{ $std->total_sks ?? 0 }} SKS Diselesaikan</small>
                                        </td>
                                        <td>
                                            @if($activeAppsCount > 0)
                                                <span class="badge badge-outline-primary">
                                                    <i class="la la-file-alt me-1"></i> Melamar di {{ $activeAppsCount }} Tempat
                                                </span>
                                            @else
                                                <span class="badge badge-light text-muted">Belum ada lamaran</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($currentLec)
                                                <div>
                                                    <span class="badge badge-success px-2 py-1">
                                                        <i class="la la-user-check me-1"></i> {{ $currentLec->user->name }}
                                                    </span>
                                                    <small class="d-block text-muted mt-1">Beban: {{ $currentLec->current_mentee_count }}/{{ $currentLec->max_mentee }}</small>
                                                </div>
                                            @else
                                                <span class="badge badge-warning text-dark px-2 py-1">
                                                    <i class="la la-clock me-1"></i> Belum Ditetapkan
                                                </span>
                                            @endif
                                        </td>
                                        <td style="min-width: 320px;">
                                            <form action="{{ route('kaprodi.dpl-plotting.pre-placement.assign', $std->id) }}" method="POST" class="d-flex gap-2 align-items-center">
                                                @csrf
                                                <select name="lecturer_id" class="form-control form-control-sm" required style="font-size: 0.85rem;">
                                                    <option value="">-- Pilih Dosen DPL --</option>
                                                    @foreach($lecturers as $lec)
                                                        @php
                                                            $isCurrent = $currentLec && $currentLec->id == $lec['id'];
                                                        @endphp
                                                        <option value="{{ $lec['id'] }}" 
                                                            {{ $isCurrent ? 'selected' : '' }}
                                                            {{ !$lec['has_capacity'] && !$isCurrent ? 'disabled' : '' }}>
                                                            {{ $lec['name'] }} ({{ $lec['current_mentee'] }}/{{ $lec['max_mentee'] }})
                                                            {{ $isCurrent ? ' [Saat Ini]' : '' }}
                                                            {{ !$lec['has_capacity'] && !$isCurrent ? ' [Penuh]' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-{{ $currentLec ? 'outline-primary' : 'primary' }} btn-sm px-2 text-nowrap" title="{{ $currentLec ? 'Ubah DPL' : 'Tugaskan DPL' }}">
                                                    <i class="la la-{{ $currentLec ? 'pencil' : 'user-plus' }}"></i>
                                                    {{ $currentLec ? 'Ubah' : 'Tugaskan' }}
                                                </button>
                                                @if($currentLec)
                                                    <button type="button" class="btn btn-outline-danger btn-sm px-2" title="Hapus Plotting DPL" onclick="if(confirm('Batalkan penugasan DPL untuk mahasiswa ini?')) { document.getElementById('removeForm{{ $std->id }}').submit(); }">
                                                        <i class="la la-times"></i>
                                                    </button>
                                                @endif
                                            </form>
                                            @if($currentLec)
                                                <form id="removeForm{{ $std->id }}" action="{{ route('kaprodi.dpl-plotting.pre-placement.remove', $std->id) }}" method="POST" class="d-none">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="la la-check-circle text-success fs-1 d-block mb-2"></i>
                                            Tidak ada mahasiswa pra-penempatan ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} mahasiswa
                        </small>
                        <div>
                            {{ $students->links() }}
                        </div>
                    </div>

                @elseif($tab === 'waiting')
                    <!-- ========================================== -->
                    <!-- TAB 2: MENUNGGU PENUGASAN (DITERIMA MAGANG) -->
                    <!-- ========================================== -->
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Mahasiswa</strong></th>
                                    <th><strong>Lowongan Magang</strong></th>
                                    <th><strong>Mitra Industri</strong></th>
                                    <th><strong>Pilih Dosen Pembimbing (DPL)</strong></th>
                                    <th><strong>Aksi</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($internships as $internship)
                                    <tr>
                                        <td>
                                            <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $internship->student->user->name }}</h6>
                                            <small class="text-muted">NIM: {{ $internship->student->nim }}</small>
                                        </td>
                                        <td>
                                            <span class="text-dark font-w500">{{ $internship->vacancy->title }}</span>
                                            <small class="d-block text-muted">Posisi: {{ $internship->vacancy->position ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $internship->vacancy->industry->name }}</span>
                                            <small class="d-block text-muted">{{ $internship->vacancy->industry->city ?? '-' }}</small>
                                        </td>
                                        
                                        <!-- Form Plotting DPL -->
                                        <form action="{{ route('kaprodi.dpl-plotting.assign', $internship->id) }}" method="POST">
                                            @csrf
                                            <td style="min-width: 280px;">
                                                <select name="lecturer_id" class="form-control form-control-sm" required>
                                                    <option value="">Pilih Dosen DPL...</option>
                                                    @foreach($lecturers as $lec)
                                                        <option value="{{ $lec['id'] }}" {{ !$lec['has_capacity'] ? 'disabled' : '' }}>
                                                            {{ $lec['name'] }} (Beban: {{ $lec['current_mentee'] }}/{{ $lec['max_mentee'] }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <button type="submit" class="btn btn-primary btn-sm px-3">
                                                    <i class="la la-user-plus me-1"></i> Tugaskan DPL
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="la la-check-circle text-success fs-1 d-block mb-2"></i>
                                            Tidak ada mahasiswa magang baru yang menunggu plotting DPL. Semua mahasiswa telah memiliki pembimbing!
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari {{ $internships->total() }} mahasiswa
                        </small>
                        <div>
                            {{ $internships->links() }}
                        </div>
                    </div>

                @else
                    <!-- ========================================== -->
                    <!-- TAB 3: SEDANG MAGANG AKTIF (UBAH DPL)       -->
                    <!-- ========================================== -->
                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Mahasiswa</strong></th>
                                    <th><strong>Mitra Industri</strong></th>
                                    <th><strong>Posisi Magang</strong></th>
                                    <th><strong>DPL Saat Ini</strong></th>
                                    <th><strong>Tanggal Penugasan</strong></th>
                                    <th class="text-end"><strong>Aksi</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($internships as $internship)
                                    @php
                                        $dpl = $internship->dplAssignment?->lecturer;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $internship->student->photo_url }}" width="35" height="35" class="rounded-circle me-2" style="object-fit: cover;" alt="">
                                                <div>
                                                    <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $internship->student->user->name }}</h6>
                                                    <small class="text-muted">NIM: {{ $internship->student->nim }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-dark font-w500">{{ $internship->vacancy->industry->name }}</span>
                                            <small class="d-block text-muted">{{ $internship->vacancy->industry->city ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="text-dark">{{ $internship->vacancy->position ?? $internship->vacancy->title }}</span>
                                            <small class="d-block text-muted"><span class="badge badge-xs badge-light">{{ $internship->vacancy->work_type_label }}</span></small>
                                        </td>
                                        <td>
                                            @if($dpl)
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <span class="text-dark font-w600 d-block">{{ $dpl->user->name }}</span>
                                                        <small class="text-muted">Beban: {{ $dpl->current_mentee_count }}/{{ $dpl->max_mentee }} mentee</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge badge-warning">Belum Ditugaskan</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($internship->dplAssignment?->assigned_at)
                                                <span class="text-dark">{{ $internship->dplAssignment->assigned_at->format('d M Y') }}</span>
                                                <small class="d-block text-muted">{{ $internship->dplAssignment->assigned_at->diffForHumans() }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <!-- Tombol Ubah DPL (Trigger Modal) -->
                                            <button type="button" class="btn btn-outline-warning btn-sm px-3" data-bs-toggle="modal" data-bs-target="#reassignModal{{ $internship->id }}">
                                                <i class="la la-exchange-alt me-1"></i> Ubah DPL
                                            </button>

                                            <!-- Modal Ubah DPL -->
                                            <div class="modal fade text-start" id="reassignModal{{ $internship->id }}" tabindex="-1" aria-labelledby="reassignModalLabel{{ $internship->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow">
                                                        <form action="{{ route('kaprodi.dpl-plotting.reassign', $internship->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-header bg-light border-0 pb-3">
                                                                <h5 class="modal-title font-w600 text-dark" id="reassignModalLabel{{ $internship->id }}">
                                                                    <i class="la la-exchange-alt text-warning me-1"></i> Pengalihan / Ubah DPL
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body pt-3">
                                                                <!-- Info Mahasiswa & Magang -->
                                                                <div class="p-3 bg-light rounded mb-3">
                                                                    <div class="row">
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Mahasiswa</small>
                                                                            <strong class="text-dark">{{ $internship->student->user->name }}</strong>
                                                                            <div class="text-muted small">NIM: {{ $internship->student->nim }}</div>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Mitra Industri</small>
                                                                            <strong class="text-dark">{{ $internship->vacancy->industry->name }}</strong>
                                                                            <div class="text-muted small">{{ $internship->vacancy->title }}</div>
                                                                        </div>
                                                                    </div>
                                                                    <hr class="my-2">
                                                                    <div>
                                                                        <small class="text-muted d-block">DPL Saat Ini</small>
                                                                        <span class="badge badge-outline-primary mt-1">
                                                                            <i class="la la-user me-1"></i>
                                                                            {{ $internship->dplAssignment?->lecturer?->user?->name ?? 'Belum Ditugaskan' }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <!-- Dropdown Dosen Baru -->
                                                                <div class="mb-3">
                                                                    <label class="form-label font-w600 text-dark">
                                                                        Pilih Dosen Pembimbing (DPL) Baru <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="lecturer_id" class="form-control" required>
                                                                        <option value="">-- Pilih Dosen DPL Baru --</option>
                                                                        @foreach($lecturers as $lec)
                                                                            @php
                                                                                $isCurrent = $internship->dplAssignment && $internship->dplAssignment->lecturer_id == $lec['id'];
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

                                                                <!-- Alasan Pergantian -->
                                                                <div class="mb-3">
                                                                    <label class="form-label font-w600 text-dark">
                                                                        Alasan Pergantian DPL / Catatan <span class="text-muted font-weight-normal">(Opsional)</span>
                                                                    </label>
                                                                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Penyesuaian keahlian topik magang, DPL sebelumnya cuti, dll..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-warning btn-sm text-white px-3">
                                                                    <i class="la la-check me-1"></i> Simpan & Ubah DPL
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            Tidak ada data mahasiswa dengan DPL aktif ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari {{ $internships->total() }} mahasiswa
                        </small>
                        <div>
                            {{ $internships->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
