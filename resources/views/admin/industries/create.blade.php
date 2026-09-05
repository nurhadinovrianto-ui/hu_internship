@extends('layouts.app')

@section('title', 'Tambah Mitra Baru')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Tambah Mitra Baru</h4>
            <p class="mb-0">Daftarkan perusahaan mitra baru beserta status MOU akademik.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.industries.index') }}">Mitra</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Tambah</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5">
                <form action="{{ route('admin.industries.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="name">Nama Instansi / Perusahaan <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: PT Telkom Indonesia" value="{{ old('name') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="industry_type">Bidang Industri <span class="text-danger">*</span></label>
                            <input type="text" name="industry_type" id="industry_type" class="form-control" placeholder="Contoh: IT &amp; Telekomunikasi" value="{{ old('industry_type') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="email">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="hr@perusahaan.com" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="phone">Nomor Telp Kantor <span class="text-danger">*</span></label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Contoh: 021-123456" value="{{ old('phone') }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="city">Kota <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city" class="form-control" placeholder="Contoh: Bandung" value="{{ old('city') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="province">Provinsi</label>
                            <input type="text" name="province" id="province" class="form-control" placeholder="Contoh: Jawa Barat" value="{{ old('province') }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="website">Website Resmi (Opsional)</label>
                            <input type="url" name="website" id="website" class="form-control" placeholder="https://www.perusahaan.com" value="{{ old('website') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="contact_person">Contact Person (HR/Humas)</label>
                            <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Nama penanggung jawab..." value="{{ old('contact_person') }}">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="address">Alamat Lengkap Perusahaan <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control" rows="3" required>{{ old('address') }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label class="form-label" for="description">Tentang Perusahaan / Deskripsi Singkat</label>
                        <textarea name="description" id="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                    </div>

                    <h5 class="text-dark my-4 border-top pt-4" style="font-weight: 700;">Status Kerjasama (MOU)</h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="partnership_status">Tingkat Kerjasama</label>
                            <select name="partnership_status" id="partnership_status" class="form-control">
                                <option value="none">Tanpa MOU (Mitra Umum)</option>
                                <option value="mou">MOU (Memorandum of Understanding)</option>
                                <option value="moa">MOA (Memorandum of Agreement)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="mou_start_date">Mulai MOU</label>
                            <input type="date" name="mou_start_date" id="mou_start_date" class="form-control">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label" for="mou_end_date">Berakhir MOU</label>
                            <input type="date" name="mou_end_date" id="mou_end_date" class="form-control">
                        </div>
                    </div>

                    <h5 class="text-dark my-4 border-top pt-4" style="font-weight: 700;">
                        <i class="la la-user-shield text-primary me-2"></i>Akun Login Supervisor Mitra (Otomatis Dibuat)
                    </h5>

                    <div class="alert alert-info py-2 px-3 mb-4 d-flex align-items-center" style="font-size: 13.5px; border-radius: 8px;">
                        <i class="la la-info-circle me-2 fs-5"></i>
                        <div>
                            Akun login berhak akses <strong>Supervisor Industri</strong> akan otomatis dibuatkan untuk mitra ini. Anda dapat menyesuaikan informasi akun di bawah ini atau biarkan default/kosong (sistem otomatis memakai kontak &amp; email perusahaan di atas).
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="supervisor_name">Nama Supervisor / PIC Akun</label>
                            <input type="text" name="supervisor_name" id="supervisor_name" class="form-control" placeholder="Otomatis mengikuti Contact Person jika kosong" value="{{ old('supervisor_name') }}">
                            <small class="text-muted">Kosongkan jika ingin otomatis menggunakan nama Contact Person / PIC di atas.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="supervisor_email">Email Login Akun</label>
                            <input type="email" name="supervisor_email" id="supervisor_email" class="form-control @error('supervisor_email') is-invalid @enderror" placeholder="Otomatis mengikuti email perusahaan jika kosong" value="{{ old('supervisor_email') }}">
                            @error('supervisor_email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Digunakan untuk login supervisor mitra ke portal SIMANG.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="supervisor_password">Password Awal Akun</label>
                            <input type="text" name="supervisor_password" id="supervisor_password" class="form-control" placeholder="Default: password123" value="{{ old('supervisor_password', 'password123') }}">
                            <small class="text-muted">Default: <code>password123</code></small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="supervisor_position">Jabatan / Posisi</label>
                            <input type="text" name="supervisor_position" id="supervisor_position" class="form-control" placeholder="Contoh: HR Manager / Mentor" value="{{ old('supervisor_position', 'Pembimbing Lapangan / HR') }}">
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label" for="supervisor_division">Divisi / Departemen</label>
                            <input type="text" name="supervisor_division" id="supervisor_division" class="form-control" placeholder="Contoh: Human Resources" value="{{ old('supervisor_division', 'Human Resources') }}">
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
                        <a href="{{ route('admin.industries.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-check me-1"></i> Buat Mitra &amp; Akun Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactInput = document.getElementById('contact_person');
    const supNameInput = document.getElementById('supervisor_name');
    const emailInput = document.getElementById('email');
    const supEmailInput = document.getElementById('supervisor_email');

    contactInput?.addEventListener('input', function() {
        if (!supNameInput.value) {
            supNameInput.placeholder = this.value ? this.value : 'Otomatis mengikuti Contact Person jika kosong';
        }
    });

    emailInput?.addEventListener('input', function() {
        if (!supEmailInput.value) {
            supEmailInput.placeholder = this.value ? this.value : 'Otomatis mengikuti email perusahaan jika kosong';
        }
    });
});
</script>
@endpush
@endsection
