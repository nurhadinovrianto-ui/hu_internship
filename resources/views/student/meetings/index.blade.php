@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Jadwal Online Meeting</h4>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Meetings</a></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Meeting Saya</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET" class="row g-2 mb-4 align-items-center">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text bg-white"><i class="la la-search"></i></span>
                                <input type="text" name="search" class="form-control" placeholder="Cari topik atau nama dosen/pembimbing..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select name="status" class="form-control" onchange="this.form.submit()">
                                <option value="">Semua Status Meeting</option>
                                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Sedang Berlangsung</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
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
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th><strong>Topik</strong></th>
                                    <th><strong>Host (Penyelenggara)</strong></th>
                                    <th><strong>Waktu (Jadwal)</strong></th>
                                    <th><strong>Status</strong></th>
                                    <th><strong>Aksi</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($meetings as $meeting)
                                <tr>
                                    <td>{{ $meeting->topic }}</td>
                                    <td>{{ $meeting->host->name }} ({{ $meeting->host->roles->first()->name }})</td>
                                    <td>{{ $meeting->start_time->format('d M Y, H:i') }}</td>
                                    <td>
                                        @if($meeting->status == 'scheduled')
                                            <span class="badge light badge-warning">Terjadwal</span>
                                        @elseif($meeting->status == 'active')
                                            <span class="badge light badge-success">Sedang Berlangsung</span>
                                        @elseif($meeting->status == 'completed')
                                            <span class="badge light badge-info">Selesai</span>
                                        @else
                                            <span class="badge light badge-danger">Dibatalkan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($meeting->status !== 'completed' && $meeting->status !== 'cancelled')
                                            <a href="{{ route('meetings.join', $meeting->id) }}" class="btn btn-success shadow btn-xs me-1"><i class="fas fa-video me-1"></i> Gabung Meeting</a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada jadwal meeting.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($meetings, 'hasPages') && ($meetings->hasPages() || $meetings->total() > 0))
                        <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                            <small class="text-muted">Menampilkan {{ $meetings->firstItem() ?? 0 }} - {{ $meetings->lastItem() ?? 0 }} dari {{ $meetings->total() }} data</small>
                            {{ $meetings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
