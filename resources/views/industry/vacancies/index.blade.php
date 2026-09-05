@extends('layouts.app')

@section('title', 'Kelola Lowongan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kelola Lowongan Magang Industri</h4>
            <p class="mb-0">Buat lowongan magang baru, pantau pelamar masuk, dan kelola kuota penerimaan.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Kelola Lowongan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Lowongan Aktif</h4>
                <a href="{{ route('industry.vacancies.create') }}" class="btn btn-primary btn-sm px-4">
                    <i class="la la-plus me-1"></i> Terbitkan Lowongan Baru
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Posisi Pekerjaan</strong></th>
                                <th><strong>Divisi</strong></th>
                                <th><strong>Kuota</strong></th>
                                <th><strong>Pelamar Masuk</strong></th>
                                <th><strong>Batas Pendaftaran</strong></th>
                                <th><strong>Status Lowongan</strong></th>
                                <th><strong>Aksi Kelola</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vacancies as $vac)
                                <tr>
                                    <td class="text-dark" style="font-weight: 600;">{{ $vac->title }}</td>
                                    <td>{{ $vac->division ?? '-' }}</td>
                                    <td>{{ $vac->quota }} Orang</td>
                                    <td>
                                        <a href="{{ route('industry.applicants.index', $vac->id) }}" class="badge badge-info text-white font-weight-bold px-3 py-2">
                                            <i class="la la-users me-1"></i> {{ $vac->applications_count }} Pelamar Baru
                                        </a>
                                    </td>
                                    <td>{{ $vac->apply_deadline->format('d M Y') }}</td>
                                    <td>
                                        <span class="badge {{ $vac->status_badge['class'] }} text-white">
                                            {{ $vac->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <!-- Open/Close Toggle -->
                                            <form action="{{ route('industry.vacancies.toggle-status', $vac->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-xs" title="Tutup / Buka Lowongan">
                                                    <i class="la la-power-off"></i>
                                                </button>
                                            </form>

                                            <!-- Show detail -->
                                            <a href="{{ route('industry.vacancies.show', $vac->id) }}" class="btn btn-info btn-xs" title="Lihat Detail &amp; Edit">
                                                <i class="la la-eye"></i>
                                            </a>

                                            <!-- Delete -->
                                            <form action="{{ route('industry.vacancies.destroy', $vac->id) }}" method="POST" onsubmit="return confirm('Hapus lowongan ini permanen?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-xs" title="Hapus Lowongan">
                                                    <i class="la la-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada lowongan magang yang Anda terbitkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $vacancies->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
