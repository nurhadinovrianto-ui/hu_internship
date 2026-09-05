@extends('layouts.app')

@section('title', 'Kriteria Penilaian Industri (Dinamis)')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Kriteria Penilaian Perusahaan</h4>
            <p class="mb-0">Sesuaikan indikator &amp; bobot rubrik penilaian performa magang sesuai standar perusahaan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('industry.assessment.index') }}">Penilaian</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Kriteria Dinamis</a></li>
        </ol>
    </div>
</div>

@if($isUsingDefault)
<div class="alert alert-info border-0 shadow-sm p-4 mb-4" style="border-radius: 12px; background-color: #EFF6FF; color: #1E3A8A;">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="mb-1 font-weight-bold" style="color: #1E3A8A;">
                <i class="la la-info-circle me-2"></i> Anda Saat Ini Menggunakan Kriteria Penilaian Standar
            </h5>
            <p class="mb-0" style="font-size: 14px;">
                Jika perusahaan Anda ingin menentukan bobot atau indikator penilaian khusus, silakan klik tombol di samping untuk mulai menyesuaikan.
            </p>
        </div>
        <form action="{{ route('industry.assessment-criteria.customize') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary font-weight-bold px-4">
                <i class="la la-sliders me-1"></i> Kustomisasi Kriteria Perusahaan
            </button>
        </form>
    </div>
</div>
@endif

<div class="row">
    <!-- Tabel Kriteria -->
    <div class="col-xl-8 col-lg-8">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0 font-weight-bold">Daftar Indikator Penilaian</h5>
                @php $totalWeight = $criteria->sum('weight'); @endphp
                <span class="badge {{ $totalWeight == 100 ? 'bg-success' : 'bg-warning' }} text-white px-3 py-2" style="font-size: 13px;">
                    Total Bobot: {{ $totalWeight }}%
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><strong>No</strong></th>
                                <th><strong>Nama Kriteria</strong></th>
                                <th><strong>Bobot (%)</strong></th>
                                <th><strong>Keterangan</strong></th>
                                @if(!$isUsingDefault)
                                    <th class="text-end"><strong>Aksi</strong></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($criteria as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <h6 class="mb-0 font-weight-bold text-dark">{{ $item->name }}</h6>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white font-weight-bold px-3 py-1">
                                            {{ $item->weight }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-muted" style="font-size: 13px;">
                                            {{ $item->description ?: '-' }}
                                        </span>
                                    </td>
                                    @if(!$isUsingDefault)
                                        <td class="text-end">
                                            <form action="{{ route('industry.assessment-criteria.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus kriteria ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm px-2">
                                                    <i class="la la-trash"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada kriteria yang dikonfigurasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tambah Kriteria Khusus Perusahaan -->
    @if(!$isUsingDefault)
    <div class="col-xl-4 col-lg-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header">
                <h5 class="mb-0 font-weight-bold">Tambah Kriteria Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('industry.assessment-criteria.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Kriteria</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Kualitas Output Proyek" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Bobot Penilaian (%)</label>
                        <input type="number" step="0.5" name="weight" class="form-control" placeholder="Contoh: 25" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Penjelasan indikator penilaian..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 font-weight-bold py-2">
                        <i class="la la-plus-circle me-1"></i> Simpan Kriteria
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
