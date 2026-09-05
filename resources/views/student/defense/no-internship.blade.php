@extends('layouts.app')

@section('title', 'Seminar / Sidang Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Seminar / Sidang Ujian Magang</h4>
            <p class="mb-0">Pendaftaran dan pemantauan jadwal sidang evaluasi magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Sidang Magang</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0 text-center py-5">
            <div class="card-body">
                <i class="la la-graduation-cap text-muted mb-3" style="font-size: 54px;"></i>
                <h4 class="text-dark fw-bold">Belum Ada Program Magang Aktif</h4>
                <p class="text-muted">Pendaftaran seminar/sidang ujian magang hanya dapat diakses setelah Anda memiliki program magang yang sedang berjalan atau selesai.</p>
                <a href="{{ route('student.dashboard') }}" class="btn btn-primary btn-sm mt-2">
                    <i class="la la-arrow-left me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
