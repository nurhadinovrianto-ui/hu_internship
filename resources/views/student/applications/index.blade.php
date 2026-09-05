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
                <form action="{{ url()->current() }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Cari posisi, judul atau perusahaan..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Status Lamaran</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Review Kaprodi</option>
                            <option value="kaprodi_approved" {{ request('status') === 'kaprodi_approved' ? 'selected' : '' }}>Disetujui Kaprodi (Menunggu Industri)</option>
                            <option value="kaprodi_rejected" {{ request('status') === 'kaprodi_rejected' ? 'selected' : '' }}>Ditolak Kaprodi</option>
                            <option value="industry_accepted" {{ request('status') === 'industry_accepted' ? 'selected' : '' }}>Diterima Industri</option>
                            <option value="industry_rejected" {{ request('status') === 'industry_rejected' ? 'selected' : '' }}>Ditolak Industri</option>
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
                
                @if($applications->hasPages() || $applications->total() > 0)
                    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">Menampilkan {{ $applications->firstItem() ?? 0 }} - {{ $applications->lastItem() ?? 0 }} dari {{ $applications->total() }} data</small>
                        {{ $applications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
