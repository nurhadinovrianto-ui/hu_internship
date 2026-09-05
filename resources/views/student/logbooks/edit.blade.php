@extends('layouts.app')

@section('title', 'Revisi Logbook Jurnal Harian')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Revisi Logbook</h4>
            <p class="mb-0">Perbaiki laporan aktivitas Anda berdasarkan masukan dari pembimbing.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.logbooks.index') }}">Logbook</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Revisi</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-body p-5">
                <form action="{{ route('student.logbooks.update', $logbook->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="date">Tanggal Aktivitas</label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ $logbook->date->toDateString() }}" disabled>
                            <small class="text-muted">Tanggal tidak dapat diubah.</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="title">Judul Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Contoh: Pembuatan mockup modul landing page" value="{{ old('title', $logbook->title) }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="description">Deskripsi Aktivitas Secara Detail <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="8" placeholder="Jelaskan secara rinci apa saja pekerjaan yang Anda lakukan hari ini..." required>{{ old('description', $logbook->description) }}</textarea>
                        <small class="text-muted">Minimal menuliskan 20 karakter.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="learning_outcomes">Hasil Pembelajaran / Technical Skills yang Didapat</label>
                        <textarea name="learning_outcomes" id="learning_outcomes" class="form-control" rows="4" placeholder="Tuliskan pengetahuan baru atau skill teknis baru yang Anda pelajari hari ini...">{{ old('learning_outcomes', $logbook->learning_outcomes) }}</textarea>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="attachment">File Pendukung / Lampiran Kerja (Opsional)</label>
                        @if($logbook->attachment)
                            <div class="mb-2">
                                <a href="{{ asset('storage/' . $logbook->attachment) }}" target="_blank" class="text-primary"><i class="la la-paperclip"></i> Lampiran saat ini</a>
                            </div>
                        @endif
                        <input type="file" name="attachment" id="attachment" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah lampiran lama.</small>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('student.logbooks.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">Simpan Revisi Logbook</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
