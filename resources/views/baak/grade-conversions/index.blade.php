@extends('layouts.app')

@section('title', 'Konversi Nilai Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Konversi Nilai Akhir Magang</h4>
            <p class="mb-0">Gabungkan penilaian Akademik &amp; Industri dan input konversi SKS mata kuliah pengganti.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">BAAK</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Konversi Nilai</a></li>
        </ol>
        <div class="ms-auto">
            <a href="{{ route('baak.grade-conversions.export') }}" class="btn btn-success text-white">
                <i class="la la-file-excel me-1"></i> Export Rekap Nilai
            </a>
        </div>
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
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Nilai Industri (40%)</strong></th>
                                <th><strong>Nilai DPL (60%)</strong></th>
                                <th><strong>Nilai Gabungan</strong></th>
                                <th><strong>Konversi SKS &amp; MK Pengganti</strong></th>
                                <th><strong>Aksi Konversi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $internship)
                                @php
                                    $dplAssessment = $internship->assessments->firstWhere('assessor_type', 'dpl');
                                    $industryAssessment = $internship->assessments->firstWhere('assessor_type', 'industry');
                                    $conversion = $internship->gradeConversion;
                                    
                                    $hasAssessments = $dplAssessment && $industryAssessment;
                                    $finalCombinedScore = 0;
                                    if ($hasAssessments) {
                                        $finalCombinedScore = ($industryAssessment->final_score * 0.4) + ($dplAssessment->final_score * 0.6);
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark">{{ $internship->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $internship->student->nim }}</small>
                                    </td>
                                    <td>{{ $internship->vacancy->industry->name }}</td>
                                    <td>
                                        @if($industryAssessment)
                                            <span class="text-dark font-weight-bold">{{ $industryAssessment->final_score }}</span>
                                        @else
                                            <span class="text-danger font-weight-bold">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dplAssessment)
                                            <span class="text-dark font-weight-bold">{{ $dplAssessment->final_score }}</span>
                                        @else
                                            <span class="text-danger font-weight-bold">Belum Dinilai</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($hasAssessments)
                                            <span class="badge badge-success text-white font-weight-bold" style="font-size: 13px;">
                                                {{ round($finalCombinedScore, 2) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    
                                    <!-- Form Konversi Inline -->
                                    <form action="{{ route('baak.grade-conversions.store', $internship->id) }}" method="POST">
                                        @csrf
                                        
                                        <td style="min-width: 250px;">
                                            @if($conversion)
                                                <div class="text-dark font-weight-bold" style="font-size: 13px;">
                                                    {{ $conversion->sks_converted }} SKS &bull; {{ $conversion->mata_kuliah_pengganti }}
                                                </div>
                                                <small class="text-success font-weight-bold">Grade: {{ $conversion->letter_grade }} (GP: {{ $conversion->grade_point }})</small>
                                            @else
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <input type="number" name="sks_converted" class="form-control form-control-sm" placeholder="SKS" min="1" max="24" required {{ !$hasAssessments ? 'disabled' : '' }}>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" name="mata_kuliah_pengganti" class="form-control form-control-sm" placeholder="Mata Kuliah Pengganti..." required {{ !$hasAssessments ? 'disabled' : '' }}>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($conversion)
                                                <span class="badge badge-success text-white font-weight-bold py-2 px-3">FINALIZED</span>
                                            @else
                                                <button type="submit" class="btn btn-success text-white btn-sm px-3" {{ !$hasAssessments ? 'disabled' : '' }}>
                                                    <i class="la la-exchange-alt me-1"></i> Konversi
                                                </button>
                                            @endif
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada magang selesai yang memerlukan konversi nilai.</td>
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
