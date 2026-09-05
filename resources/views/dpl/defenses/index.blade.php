@extends('layouts.app')

@section('title', 'Penilaian Sidang Magang - DPL')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Sidang Seminar Magang</h4>
            <p class="mb-0">Daftar mahasiswa yang Anda bimbing atau Anda uji pada seminar evaluasi magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Sidang Magang</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Jadwal & Penilaian Sidang</h4>
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
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Peran Anda</strong></th>
                                <th><strong>Jadwal Pelaksanaan</strong></th>
                                <th><strong>Ruang / Tautan</strong></th>
                                <th><strong>Status Sidang</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($defenses as $def)
                                @php
                                    $isExaminer = ($def->examiner_lecturer_id === $lecturer->id);
                                    $myScore = $def->scores->where('evaluator_id', auth()->id())->first();
                                @endphp
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $def->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $def->student->nim }} &bull; {{ $def->internship->vacancy->industry->name }}</small>
                                    </td>
                                    <td>
                                        @if($isExaminer)
                                            <span class="badge light badge-primary"><i class="la la-user-check me-1"></i> Dosen Penguji</span>
                                        @else
                                            <span class="badge light badge-info"><i class="la la-user-tie me-1"></i> Dosen Pembimbing</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($def->scheduled_date)
                                            <span class="text-dark fw-medium">{{ $def->scheduled_date->format('d M Y') }}</span>
                                            <br><small class="text-muted">{{ substr($def->start_time, 0, 5) }} - {{ substr($def->end_time, 0, 5) }} WIB</small>
                                        @else
                                            <span class="badge light badge-warning">Belum Ada Jadwal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($def->room_or_link && Str::startsWith($def->room_or_link, 'http'))
                                            <a href="{{ $def->room_or_link }}" target="_blank" class="text-primary fw-medium">
                                                <i class="la la-video"></i> Buka Link Sidang
                                            </a>
                                        @else
                                            <span class="text-dark">{{ $def->room_or_link ?? '-' }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $def->status_badge['class'] }}">
                                            {{ $def->status_badge['label'] }}
                                        </span>
                                        @if($myScore)
                                            <br><small class="text-success"><i class="la la-check"></i> Dinilai: {{ $myScore->average_score }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('dpl.defenses.assess', $def->id) }}" class="btn btn-primary btn-sm">
                                                <i class="la la-pencil-alt me-1"></i> {{ $myScore ? 'Ubah Nilai' : 'Beri Nilai' }}
                                            </a>
                                            @if($def->status === 'passed')
                                                <a href="{{ route('dpl.defenses.beritaAcara', $def->id) }}" target="_blank" class="btn btn-outline-success btn-sm" title="Cetak Berita Acara">
                                                    <i class="la la-print"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="la la-calendar-times mb-2" style="font-size: 36px;"></i>
                                        <p class="mb-0">Belum ada mahasiswa yang dijadwalkan untuk sidang bimbingan atau ujian Anda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $defenses->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
