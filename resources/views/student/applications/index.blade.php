@extends('layouts.app')

@section('title', 'Lamaran Saya')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Lamaran Magang Saya</h4>
            <p class="mb-0">Pantau status persetujuan Kaprodi dan hasil seleksi dari mitra industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Lamaran Saya</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Lowongan Magang</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Tanggal Pengajuan</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $app->vacancy->title }}</h6>
                                        <small class="text-muted">{{ $app->vacancy->work_type_label }} &bull; {{ $app->vacancy->location ?? 'Remote' }}</small>
                                    </td>
                                    <td>{{ $app->vacancy->industry->name }}</td>
                                    <td>{{ $app->created_at->format('d M Y, H:i') }} WIB</td>
                                    <td>
                                        <span class="badge {{ $app->status_badge['class'] }} py-2 px-3">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('student.applications.show', $app->id) }}" class="btn btn-primary btn-sm px-3">Tracking Progress</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada lamaran magang yang dikirim.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4 d-flex justify-content-center">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
