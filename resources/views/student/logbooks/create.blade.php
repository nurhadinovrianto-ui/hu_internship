@extends('layouts.app')

@section('title', 'Tulis Logbook Jurnal Harian')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Tulis Logbook Baru</h4>
            <p class="mb-0">Laporkan aktivitas, rencana kegiatan, serta hasil pembelajaran magang harian Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.logbooks.index') }}">Logbook</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Tulis Baru</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                <form action="{{ route('student.logbooks.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date">Tanggal Aktivitas <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="date" class="form-control" max="{{ now()->toDateString() }}" value="{{ old('date', now()->toDateString()) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Pembuatan mockup modul landing page" value="{{ old('title') }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="description">Deskripsi Aktivitas Secara Detail <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="8" placeholder="Jelaskan secara rinci apa saja pekerjaan yang Anda lakukan hari ini..." required>{{ old('description') }}</textarea>
                        <small class="text-muted">Minimal menuliskan 20 karakter.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="learning_outcomes">Hasil Pembelajaran / Technical Skills yang Didapat</label>
                        <textarea name="learning_outcomes" id="learning_outcomes" class="form-control" rows="4" placeholder="Tuliskan pengetahuan baru atau skill teknis baru yang Anda pelajari hari ini...">{{ old('learning_outcomes') }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="attachment">File Pendukung / Lampiran Kerja (Opsional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                        <small class="text-muted">Bisa berupa screenshot (.png/.jpg), kode program (.zip), atau dokumen penunjang (.pdf). Maksimal 5MB.</small>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('student.logbooks.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Kirim Jurnal Logbook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
