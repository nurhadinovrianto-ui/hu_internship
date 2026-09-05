@extends('layouts.app')

@section('title', 'Daftar Pengajuan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Validasi Pengajuan Magang</h4>
            <p class="mb-0">Daftar permohonan magang mahasiswa yang memerlukan persetujuan Ketua Program Studi.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Validasi Pengajuan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Search/Filters -->
                <form action="{{ route('kaprodi.applications.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mahasiswa, NIM, posisi, industri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Pengajuan</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Validasi</option>
                            <option value="kaprodi_approved" {{ request('status') === 'kaprodi_approved' ? 'selected' : '' }}>Disetujui Kaprodi</option>
                            <option value="kaprodi_rejected" {{ request('status') === 'kaprodi_rejected' ? 'selected' : '' }}>Ditolak Kaprodi</option>
                            <option value="industry_accepted" {{ request('status') === 'industry_accepted' ? 'selected' : '' }}>Diterima Industri</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('kaprodi.applications.index') }}" class="btn btn-light" title="Reset">
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
                                <th><strong>Diajukan Pada</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $app)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $app->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $app->student->nim }}</small>
                                    </td>
                                    <td>{{ $app->vacancy->title }}</td>
                                    <td>{{ $app->vacancy->industry->name }}</td>
                                    <td>{{ $app->created_at->format('d M Y, H:i') }} WIB</td>
                                    <td>
                                        <span class="badge {{ $app->status_badge['class'] }}">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('kaprodi.applications.show', $app->id) }}" class="btn btn-primary btn-sm px-3">Review &amp; Aksi</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada pengajuan magang masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $applications->firstItem() ?? 0 }} - {{ $applications->lastItem() ?? 0 }} dari {{ $applications->total() }} pengajuan
                    </small>
                    <div>
                        {{ $applications->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
