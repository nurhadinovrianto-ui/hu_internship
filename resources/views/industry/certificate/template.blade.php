@extends('layouts.app')

@section('title', 'Desain & Pengaturan Template Sertifikat Perusahaan')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Desain Template Sertifikat Perusahaan</h4>
            <p class="mb-0">Unggah bingkai/background sertifikat A4 Landscape serta atur pejabat penandatangan perusahaan Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex gap-2">
        <a href="{{ route('industry.certificates.index') }}" class="btn btn-secondary font-weight-bold btn-sm px-3 py-2">
            <i class="la la-arrow-left me-1"></i> Kembali ke Daftar Sertifikat
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header">
                <h5 class="mb-0 font-weight-bold">Pengaturan Identitas &amp; Latar Belakang</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('industry.certificates.template.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Nama Pejabat Penandatangan</label>
                        <input type="text" name="signatory_name" class="form-control" value="{{ old('signatory_name', $template?->signatory_name) }}" placeholder="Contoh: Ir. Budi Santoso, M.T." required>
                        <small class="text-muted">Nama yang akan dicetak di bagian bawah sertifikat.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Jabatan Pejabat Penandatangan</label>
                        <input type="text" name="signatory_position" class="form-control" value="{{ old('signatory_position', $template?->signatory_position) }}" placeholder="Contoh: HR Director / Branch Manager" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Gambar Background Sertifikat (A4 Landscape)</label>
                        <input type="file" name="background_image" class="form-control" accept=".png,.jpg,.jpeg">
                        <small class="text-muted d-block mt-1">
                            Disarankan ukuran A4 Landscape (misal: 2970x2100 px atau 1122x793 px format PNG/JPG). Kosongkan jika ingin menggunakan latar belakang bingkai standar sistem.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label font-weight-bold">Gambar Tanda Tangan / Cap Perusahaan (Opsional)</label>
                        <input type="file" name="seal_image" class="form-control" accept=".png,.jpg,.jpeg">
                        <small class="text-muted d-block mt-1">Format PNG transparan disarankan.</small>
                    </div>

                    <button type="submit" class="btn btn-primary font-weight-bold w-100 py-2">
                        <i class="la la-save me-1"></i> Simpan Desain Sertifikat
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-lg-6">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="card-header">
                <h5 class="mb-0 font-weight-bold">Preview Background Saat Ini</h5>
            </div>
            <div class="card-body text-center">
                @if($template && $template->background_image)
                    <img src="{{ asset('storage/' . $template->background_image) }}" alt="Background Sertifikat" class="img-fluid border shadow-sm mb-3" style="border-radius: 8px; max-height: 380px;">
                    <p class="text-success font-weight-bold mb-0">
                        <i class="la la-check-circle me-1"></i> Background khusus perusahaan Anda aktif.
                    </p>
                @else
                    <div class="py-5 border rounded bg-light mb-3">
                        <i class="la la-image text-muted d-block mb-2" style="font-size: 56px;"></i>
                        <p class="text-muted mb-0">Belum ada gambar background khusus yang diunggah.</p>
                        <small class="text-muted">Sistem akan menggunakan desain bingkai emas standar.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
