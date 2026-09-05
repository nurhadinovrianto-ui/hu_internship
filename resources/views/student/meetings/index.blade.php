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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
