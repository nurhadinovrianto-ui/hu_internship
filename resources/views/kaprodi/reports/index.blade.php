@extends('layouts.app')

@section('title', 'Penerimaan Laporan Akhir')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Laporan Akhir Magang</h4>
            <p class="mb-0">Daftar laporan akhir yang telah disetujui DPL dan diserahkan ke Kaprodi.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Laporan Akhir</a></li>
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
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Judul Laporan</strong></th>
                                <th><strong>DPL Penilai</strong></th>
                                <th><strong>Dokumen</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reports as $rep)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $rep->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $rep->student->nim }}</small>
                                    </td>
                                    <td>{{ Str::limit($rep->title, 55) }}</td>
                                    <td>{{ $rep->reviewer?->name ?? '-' }}</td>
                                    <td>
                                        <a href="{{ asset('storage/' . $rep->file_path) }}" target="_blank" class="badge light badge-primary px-3 py-1 font-weight-bold">
                                            <i class="la la-file-pdf"></i> Lihat PDF
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $rep->status_badge['class'] }} text-white">{{ $rep->status_badge['label'] }}</span>
                                    </td>
                                    <td>
                                        @if($rep->status === 'dpl_approved')
                                            <form action="{{ route('kaprodi.reports.receive', $rep->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menerima laporan ini?');">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm px-3">
                                                    <i class="la la-check"></i> Terima Laporan
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-muted"><small><i>Telah Diterima</i></small></span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Belum ada laporan akhir yang diserahkan ke Kaprodi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
