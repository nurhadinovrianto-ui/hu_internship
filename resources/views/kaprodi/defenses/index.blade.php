@extends('layouts.app')

@section('title', 'Penjadwalan Sidang Magang - Kaprodi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Penjadwalan Seminar & Sidang Magang</h4>
            <p class="mb-0">Atur jadwal pelaksanaan ujian seminar magang dan penunjukan dewan dosen penguji.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('kaprodi.dashboard') }}">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Sidang Magang</a></li>
        </ol>
    </div>
</div>

<!-- 4 EDUMIN STAT CARDS -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-graduation-cap" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Pengajuan</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['total'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Semua Peserta</small>
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
                        <i class="la la-calendar-plus" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Perlu Jadwal</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['registered'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Menunggu Plotting</small>
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
                        <i class="la la-calendar-check" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Terjadwal</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['scheduled'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Siap Diuji</small>
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
                        <i class="la la-trophy" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Lulus Sidang</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['passed'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Selesai Ujian</small>
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
                <h4 class="card-title">Daftar Sidang Magang Prodi {{ $prodi?->name }}</h4>
            </div>

            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Filter -->
                <form action="{{ route('kaprodi.defenses.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-0"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control bg-light border-0" placeholder="Cari nama mahasiswa, NIM, industri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Sidang</option>
                            <option value="registered" {{ request('status') === 'registered' ? 'selected' : '' }}>Perlu Jadwal</option>
                            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                            <option value="passed" {{ request('status') === 'passed' ? 'selected' : '' }}>Lulus</option>
                            <option value="revision" {{ request('status') === 'revision' ? 'selected' : '' }}>Revisi</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('kaprodi.defenses.index') }}" class="btn btn-light" title="Reset">
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
                                <th><strong>Perusahaan Mitra</strong></th>
                                <th><strong>Jadwal Pelaksanaan</strong></th>
                                <th><strong>Dosen Penguji</strong></th>
                                <th><strong>Nilai & Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($defenses as $def)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $def->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $def->student->nim }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ $def->internship->vacancy->industry->name }}</span>
                                    </td>
                                    <td>
                                        @if($def->scheduled_date)
                                            <span class="text-dark fw-medium">{{ $def->scheduled_date->format('d M Y') }}</span>
                                            <br><small class="text-muted"><i class="la la-clock"></i> {{ substr($def->start_time, 0, 5) }} - {{ substr($def->end_time, 0, 5) }} WIB</small>
                                            <br><small class="text-primary"><i class="la la-map-marker"></i> {{ Str::limit($def->room_or_link, 25) }}</small>
                                        @else
                                            <span class="badge light badge-warning">Belum Dijadwalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($def->examiner)
                                            <span class="text-dark fw-medium">{{ $def->examiner->user->name }}</span>
                                            <br><small class="text-muted">DPL: {{ $def->supervisor?->user?->name ?? '-' }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Belum Ditunjuk</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $def->status_badge['class'] }}">
                                            {{ $def->status_badge['label'] }}
                                        </span>
                                        @if($def->final_score)
                                            <br><strong class="text-success">{{ $def->final_score }} ({{ $def->grade_letter }})</strong>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#scheduleModal-{{ $def->id }}">
                                            <i class="la la-calendar-plus me-1"></i> {{ $def->scheduled_date ? 'Ubah Jadwal' : 'Plotting Jadwal' }}
                                        </button>

                                        <!-- Modal Schedule -->
                                        <div class="modal fade" id="scheduleModal-{{ $def->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('kaprodi.defenses.schedule', $def->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title">Penjadwalan Sidang Magang</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <div class="p-3 bg-light rounded mb-3">
                                                                <h6 class="mb-1 text-dark fw-bold">{{ $def->student->user->name }} ({{ $def->student->nim }})</h6>
                                                                <small class="text-muted">Mitra: {{ $def->internship->vacancy->industry->name }}</small>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Tanggal Pelaksanaan <span class="text-danger">*</span></label>
                                                                <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $def->scheduled_date?->format('Y-m-d')) }}" required>
                                                            </div>

                                                            <div class="row g-2 mb-3">
                                                                <div class="col-6">
                                                                    <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                                                                    <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $def->start_time ? substr($def->start_time, 0, 5) : '09:00') }}" required>
                                                                </div>
                                                                <div class="col-6">
                                                                    <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                                                                    <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $def->end_time ? substr($def->end_time, 0, 5) : '10:30') }}" required>
                                                                </div>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Ruangan atau Link Virtual Meeting <span class="text-danger">*</span></label>
                                                                <input type="text" name="room_or_link" class="form-control" placeholder="Cth: Ruang Sidang 204 atau https://meet.google.com/..." value="{{ old('room_or_link', $def->room_or_link) }}" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label">Pilih Dosen Penguji Mandiri <span class="text-danger">*</span></label>
                                                                <select name="examiner_lecturer_id" class="form-control form-select" required>
                                                                    <option value="">-- Pilih Dosen Penguji --</option>
                                                                    @foreach($lecturers as $lec)
                                                                        <option value="{{ $lec->id }}" {{ (old('examiner_lecturer_id', $def->examiner_lecturer_id) == $lec->id) ? 'selected' : '' }}>
                                                                            {{ $lec->user->name }} (NIDN: {{ $lec->nidn ?? '-' }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                            <button type="submit" class="btn btn-primary">Simpan Jadwal Sidang</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="la la-calendar-times mb-2" style="font-size: 36px;"></i>
                                        <p class="mb-0">Tidak ada data pendaftaran sidang magang.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $defenses->firstItem() ?? 0 }} - {{ $defenses->lastItem() ?? 0 }} dari {{ $defenses->total() }} sidang
                    </small>
                    <div>
                        {{ $defenses->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
