@extends('layouts.app')

@section('title', 'Internship Agreement')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Internship Agreement</h4>
            <p class="mb-0">Dokumen kesepakatan magang dari perusahaan mitra.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Internship Agreement</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-file-alt text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Akses Ditutup</h3>
                    <p class="text-muted mx-auto" style="max-width: 600px; font-size: 15px; line-height: 1.6;">
                        {{ $reason }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 mt-3">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-xl-8 col-lg-8 col-md-12 mx-auto">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title text-white mb-0">Detail Internship Agreement</h5>
                </div>
                <div class="card-body p-4">
                    @if($agreement)
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th width="35%" class="text-muted">Nomor Surat / Dokumen</th>
                                    <td><strong>{{ $agreement->agreement_number ?? '-' }}</strong></td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Judul Kesepakatan</th>
                                    <td>{{ $agreement->title }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Periode Magang</th>
                                    <td>{{ $agreement->start_date ? $agreement->start_date->format('d M Y') : '-' }} s/d {{ $agreement->end_date ? $agreement->end_date->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Uang Saku (Allowance)</th>
                                    <td>{{ $agreement->allowance ? 'Rp ' . number_format($agreement->allowance, 0, ',', '.') : 'Tidak ada/Tidak disebutkan' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Catatan dari Industri</th>
                                    <td>{{ $agreement->notes ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="text-muted">Status Kesepakatan</th>
                                    <td>
                                        <span class="badge {{ $agreement->status_badge['class'] }}">{{ $agreement->status_badge['label'] }}</span>
                                    </td>
                                </tr>
                                @if($agreement->document_file)
                                <tr>
                                    <th class="text-muted align-middle">Dokumen Fisik (PDF)</th>
                                    <td>
                                        <a href="{{ asset('storage/' . $agreement->document_file) }}" target="_blank" class="btn btn-outline-primary btn-sm mt-2">
                                            <i class="la la-download me-1"></i> Unduh / Lihat Dokumen
                                        </a>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="la la-clock d-block mb-3" style="font-size: 56px;"></i>
                            <h5 class="text-dark">Belum Ada Kesepakatan Magang</h5>
                            <p class="mb-0">Perusahaan tempat Anda magang belum mengunggah dokumen Internship Agreement.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
