@extends('layouts.app')

@section('title', 'Monitoring Presensi Mahasiswa')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Monitoring Presensi</h4>
            <p class="mb-0">Pantau kehadiran mahasiswa bimbingan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Presensi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Daftar Presensi Mahasiswa</h4>
                <a href="{{ route('dpl.tracking.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="la la-map-marked-alt me-1"></i> Lacak Mahasiswa Realtime
                </a>
            </div>
            <div class="card-body">
                <!-- Filter & Search -->
                <form action="{{ route('dpl.attendance.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama atau NIM..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}" title="Filter Tanggal">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Kehadiran</option>
                            <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                            <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                            <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                            <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Tidak Hadir (Alpa)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'date', 'status', 'approval_status']))
                            <a href="{{ route('dpl.attendance.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Tanggal</strong></th>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Check In</strong></th>
                                <th><strong>Check Out</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances as $att)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($att->date)->format('d M Y') }}</td>
                                    <td>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ $att->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $att->student->nim }} &bull; {{ $att->internship->vacancy->industry->name }}</small>
                                    </td>
                                    <td>{{ $att->check_in_time ?? '-' }}</td>
                                    <td>{{ $att->check_out_time ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ str_replace('badge-', 'bg-', $att->status_badge['class']) }} text-white">
                                            {{ $att->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dpl.attendance.show', $att->id) }}" class="btn btn-info btn-sm text-white">
                                            <i class="la la-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada data presensi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} presensi
                    </small>
                    <div>
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
