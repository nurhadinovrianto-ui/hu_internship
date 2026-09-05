@extends('layouts.app')

@section('title', 'Usulan Magang Mandiri')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pengajuan Magang Mandiri</h4>
            <p class="mb-0">Daftarkan inisiatif program magang mandiri di perusahaan atau instansi pilihan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Magang Mandiri</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Riwayat Pengajuan Magang Mandiri</h4>
                @if(!$hasActiveInternship)
                    <a href="{{ route('student.self-proposals.create') }}" class="btn btn-primary btn-sm">
                        <i class="la la-plus-circle me-1"></i> Ajukan Magang Baru
                    </a>
                @else
                    <span class="badge light badge-success">
                        <i class="la la-check-circle me-1"></i> Program Magang Aktif
                    </span>
                @endif
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

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Perusahaan / Instansi</strong></th>
                                <th><strong>Posisi Magang</strong></th>
                                <th><strong>Periode Magang</strong></th>
                                <th><strong>Dosen DPL</strong></th>
                                <th><strong>Review DPL</strong></th>
                                <th><strong>Status Akhir</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($proposals as $prop)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $prop->company_name }}</h6>
                                        <small class="text-muted"><i class="la la-map-marker text-primary"></i> {{ Str::limit($prop->company_address, 35) }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">{{ $prop->position_title }}</span>
                                        <br><small class="text-muted">{{ $prop->industry_sector ?? 'Sektor Umum' }}</small>
                                    </td>
                                    <td>
                                        <small class="text-dark d-block">{{ $prop->start_date->format('d M Y') }} s/d</small>
                                        <small class="text-dark">{{ $prop->end_date->format('d M Y') }}</small>
                                    </td>
                                    <td>
                                        @if($prop->dpl)
                                            <span class="text-dark fw-medium">{{ $prop->dpl->user->name }}</span>
                                            <br><small class="text-muted">NIDN: {{ $prop->dpl->nidn }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Belum diplot</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $prop->dpl_status_badge['class'] }}">
                                            {{ $prop->dpl_status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $prop->status_badge['class'] }}">
                                            {{ $prop->status_badge['label'] }}
                                        </span>
                                        @if($prop->partner_account_created)
                                            <br><small class="text-success"><i class="la la-check-circle"></i> Akun Mitra Aktif</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('student.self-proposals.show', $prop->id) }}" class="btn btn-outline-info btn-xs" title="Lihat Rincian">
                                                <i class="la la-eye"></i> Rincian
                                            </a>
                                            @if(in_array($prop->status, ['submitted', 'revision']))
                                                <a href="{{ route('student.self-proposals.edit', $prop->id) }}" class="btn btn-outline-warning btn-xs" title="Perbaiki Usulan">
                                                    <i class="la la-edit"></i> Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="la la-file-alt mb-2" style="font-size: 36px;"></i>
                                        <p class="mb-0">Belum ada pengajuan magang mandiri.</p>
                                        <small>Jika Anda diterima magang mandiri di luar lowongan kampus, klik tombol <strong>Ajukan Magang Baru</strong>.</small>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $proposals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
