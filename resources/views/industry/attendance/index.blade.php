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
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Presensi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="{{ route('industry.attendance.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama mahasiswa..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
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
                                        <h6 class="mb-0">{{ $att->student->user->name }}</h6>
                                        <small class="text-muted">{{ $att->internship->vacancy->industry->name }}</small>
                                    </td>
                                    <td>{{ $att->check_in_time }}</td>
                                    <td>{{ $att->check_out_time ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $att->status_badge['class'] }}">{{ $att->status_badge['label'] }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('industry.attendance.show', $att->id) }}" class="btn btn-info btn-sm text-white" title="Lihat Peta & Foto"><i class="la la-eye"></i></a>
                                            @if($att->approval_status === 'pending')
                                                <form action="{{ route('industry.attendance.approve', $att->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-success btn-sm text-white" title="Setujui Absensi"><i class="la la-check"></i></button>
                                                </form>
                                                
                                                <form action="{{ route('industry.attendance.reject', $att->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tolak absensi ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="reason" value="Ditolak oleh supervisor">
                                                    <button type="submit" class="btn btn-danger btn-sm text-white" title="Tolak Absensi"><i class="la la-times"></i></button>
                                                </form>
                                            @endif
                                        </div>
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

                <div class="mt-4 d-flex justify-content-center">
                    {{ $attendances->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
