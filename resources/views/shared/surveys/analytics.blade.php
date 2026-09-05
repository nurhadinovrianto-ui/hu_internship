@extends('layouts.app')

@section('title', 'Analitik Kepuasan & Evaluasi Magang - ' . ($isDekan ? 'Dekan' : 'Kaprodi'))

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Analitik Kepuasan & Evaluasi Magang</h4>
            <p class="mb-0">Hasil survei kepuasan mitra industri dan evaluasi pengalaman magang mahasiswa untuk akreditasi (BAN-PT / LAM).</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ $isDekan ? route('dekan.dashboard') : route('kaprodi.dashboard') }}">{{ $isDekan ? 'Dekan' : 'Kaprodi' }}</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Analitik Kuesioner</a></li>
        </ol>
    </div>
</div>

<!-- 4 EDUMIN STAT CARDS -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-star" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Rata-rata Skor Mitra</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['avg_industry'] }} <small style="font-size: 14px;">/ 5.0</small></h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Penilaian Kompetensi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-user-graduate" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Rata-rata Mahasiswa</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['avg_student'] }} <small style="font-size: 14px;">/ 5.0</small></h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Kepuasan Wahana</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-poll" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Responden Masuk</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['count_industry'] + $stats['count_student'] }}</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">{{ $stats['count_industry'] }} Mitra, {{ $stats['count_student'] }} Mhs</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-thumbs-up" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Rekomendasi Mitra</p>
                        <h3 class="text-white mb-0 fw-bold">{{ $stats['recommend_rate'] }}%</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Bersedia Menerima Lagi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="card-title">Data Rekapitulasi Kuesioner {{ $prodi ? 'Prodi ' . $prodi->name : 'Seluruh Fakultas' }}</h4>
                <div class="btn-group">
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'industry']) }}" class="btn btn-sm {{ $type === 'industry' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="la la-building me-1"></i> Respon Mitra Industri
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['type' => 'student']) }}" class="btn btn-sm {{ $type === 'student' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="la la-user-graduate me-1"></i> Respon Mahasiswa
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Responden & Mahasiswa</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th class="text-center"><strong>Q1</strong></th>
                                <th class="text-center"><strong>Q2</strong></th>
                                <th class="text-center"><strong>Q3</strong></th>
                                <th class="text-center"><strong>Q4</strong></th>
                                <th class="text-center"><strong>Q5</strong></th>
                                <th><strong>Rata-rata</strong></th>
                                <th><strong>Rekomendasi</strong></th>
                                <th><strong>Ulasan / Catatan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($surveys as $srv)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark fw-bold">{{ $srv->internship->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $srv->internship->student->nim }}</small><br>
                                        <small class="text-primary">Oleh: {{ $srv->respondent->name }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-medium">{{ $srv->internship->vacancy->industry->name }}</span>
                                        <br><small class="text-muted">{{ $srv->internship->vacancy->title }}</small>
                                    </td>
                                    <td class="text-center"><span class="badge light badge-primary">{{ $srv->q1_rating }}</span></td>
                                    <td class="text-center"><span class="badge light badge-primary">{{ $srv->q2_rating }}</span></td>
                                    <td class="text-center"><span class="badge light badge-primary">{{ $srv->q3_rating }}</span></td>
                                    <td class="text-center"><span class="badge light badge-primary">{{ $srv->q4_rating }}</span></td>
                                    <td class="text-center"><span class="badge light badge-primary">{{ $srv->q5_rating }}</span></td>
                                    <td>
                                        <h6 class="mb-0 text-success fw-bold">{{ number_format($srv->overall_score, 2) }}</h6>
                                    </td>
                                    <td>
                                        @if($srv->recommendation)
                                            <span class="badge light badge-success"><i class="la la-thumbs-up me-1"></i> Ya</span>
                                        @else
                                            <span class="badge light badge-danger"><i class="la la-thumbs-down me-1"></i> Tidak</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($srv->feedback_text)
                                            <small class="text-muted fst-italic">"{{ Str::limit($srv->feedback_text, 50) }}"</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        <i class="la la-poll mb-2" style="font-size: 36px;"></i>
                                        <p class="mb-0">Belum ada data kuesioner pada kategori ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $surveys->firstItem() ?? 0 }} - {{ $surveys->lastItem() ?? 0 }} dari {{ $surveys->total() }} responden
                    </small>
                    <div>
                        {{ $surveys->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
