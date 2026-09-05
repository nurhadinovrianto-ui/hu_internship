@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Edit Pengguna</h4>
            <p class="mb-0">Ubah informasi akun dasar, peran, beserta profil khusus peran pengguna.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">User</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-10 col-lg-12">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4 p-md-5">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    @if ($errors->any())
                        <div class="alert alert-danger p-3 mb-4" style="border-radius: 8px;">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <h5 class="text-dark mb-4" style="font-weight: 700;">Informasi Akun Dasar</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="email">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="phone">Nomor Telepon</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Contoh: 08123456789" value="{{ old('phone', $user->phone) }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="role">Peran / Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-control" required>
                                <option value="">Pilih Peran...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ old('role', $user->roles->first()?->name) === $role->name ? 'selected' : '' }}>
                                        @if($role->name === 'super-admin') Super Admin
                                        @elseif($role->name === 'finance') Keuangan
                                        @elseif($role->name === 'baak') BAAK
                                        @elseif($role->name === 'kaprodi') Kaprodi
                                        @elseif($role->name === 'dekan') Dekan
                                        @elseif($role->name === 'dpl') Dosen Pembimbing (DPL)
                                        @elseif($role->name === 'supervisor-industri') Supervisor Industri
                                        @elseif($role->name === 'mahasiswa') Mahasiswa
                                        @else {{ ucfirst($role->name) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="password">Kata Sandi Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 8 karakter" autocomplete="new-password">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-dark font-weight-bold" for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" autocomplete="new-password">
                        </div>
                    </div>

                    <!-- ROLE-SPECIFIC CONTAINER SECTIONS -->

                    <!-- Section: Mahasiswa -->
                    <div id="section-mahasiswa" class="role-section d-none border-top pt-4 mt-4">
                        <h5 class="text-dark mb-4" style="font-weight: 700; color: var(--primary) !important;">Profil Akademik Mahasiswa</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="nim">NIM <span class="text-danger">*</span></label>
                                <input type="text" name="nim" id="nim" class="form-control" placeholder="Contoh: 2021100001" value="{{ old('nim', $user->student->nim ?? '') }}" data-required="true">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="mhs_study_program_id">Program Studi <span class="text-danger">*</span></label>
                                <select name="study_program_id" id="mhs_study_program_id" class="form-control" data-required="true">
                                    <option value="">Pilih Program Studi...</option>
                                    @foreach($studyPrograms as $sp)
                                        <option value="{{ $sp->id }}" {{ old('study_program_id', $user->student->study_program_id ?? '') == $sp->id ? 'selected' : '' }}>{{ $sp->degree }} - {{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="batch">Angkatan (Batch)</label>
                                <input type="text" name="batch" id="batch" class="form-control" placeholder="Contoh: 2021" value="{{ old('batch', $user->student->batch ?? now()->year) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="current_semester">Semester Saat Ini <span class="text-danger">*</span></label>
                                <input type="number" name="current_semester" id="current_semester" class="form-control" placeholder="Contoh: 7" value="{{ old('current_semester', $user->student->current_semester ?? 1) }}" min="1" data-required="true">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="total_sks">Total SKS Lulus <span class="text-danger">*</span></label>
                                <input type="number" name="total_sks" id="total_sks" class="form-control" placeholder="Contoh: 110" value="{{ old('total_sks', $user->student->total_sks ?? 0) }}" min="0" data-required="true">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="gpa">IPK (GPA) <span class="text-danger">*</span></label>
                                <input type="number" name="gpa" id="gpa" class="form-control" placeholder="Contoh: 3.50" value="{{ old('gpa', $user->student->gpa ?? '0.00') }}" min="0" max="4" step="0.01" data-required="true">
                            </div>
                        </div>
                    </div>

                    <!-- Section: DPL -->
                    <div id="section-dpl" class="role-section d-none border-top pt-4 mt-4">
                        <h5 class="text-dark mb-4" style="font-weight: 700; color: var(--primary) !important;">Profil Dosen Pembimbing Lapangan</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="nip">NIP</label>
                                <input type="text" name="nip" id="nip" class="form-control" placeholder="Contoh: 19880101..." value="{{ old('nip', $user->lecturer->nip ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="nidn">NIDN</label>
                                <input type="text" name="nidn" id="nidn" class="form-control" placeholder="Contoh: 040101..." value="{{ old('nidn', $user->lecturer->nidn ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="dpl_study_program_id">Program Studi Dosen <span class="text-danger">*</span></label>
                                <select name="study_program_id" id="dpl_study_program_id" class="form-control" data-required="true">
                                    <option value="">Pilih Program Studi...</option>
                                    @foreach($studyPrograms as $sp)
                                        <option value="{{ $sp->id }}" {{ old('study_program_id', $user->lecturer->study_program_id ?? '') == $sp->id ? 'selected' : '' }}>{{ $sp->degree }} - {{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="position">Jabatan Akademik</label>
                                <input type="text" name="position" id="position" class="form-control" placeholder="Contoh: Lektor, Asisten Ahli" value="{{ old('position', $user->lecturer->position ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="specialization">Bidang Keahlian</label>
                                <input type="text" name="specialization" id="specialization" class="form-control" placeholder="Contoh: AI, Rekayasa Perangkat Lunak" value="{{ old('specialization', $user->lecturer->specialization ?? '') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="max_mentee">Batas Mahasiswa Bimbingan <span class="text-danger">*</span></label>
                                <input type="number" name="max_mentee" id="max_mentee" class="form-control" placeholder="Contoh: 10" value="{{ old('max_mentee', $user->lecturer->max_mentee ?? 5) }}" min="1" data-required="true">
                            </div>
                        </div>
                    </div>

                    <!-- Section: Kaprodi -->
                    <div id="section-kaprodi" class="role-section d-none border-top pt-4 mt-4">
                        <h5 class="text-dark mb-4" style="font-weight: 700; color: var(--primary) !important;">Penetapan Kaprodi</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="leader_study_program_id">Program Studi yang Dipimpin <span class="text-danger">*</span></label>
                                <select name="leader_study_program_id" id="leader_study_program_id" class="form-control" data-required="true">
                                    <option value="">Pilih Program Studi...</option>
                                    @foreach($studyPrograms as $sp)
                                        <option value="{{ $sp->id }}" {{ old('leader_study_program_id', $ledStudyProgram->id ?? '') == $sp->id ? 'selected' : '' }}>{{ $sp->degree }} - {{ $sp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Dekan -->
                    <div id="section-dekan" class="role-section d-none border-top pt-4 mt-4">
                        <h5 class="text-dark mb-4" style="font-weight: 700; color: var(--primary) !important;">Penetapan Dekan</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="leader_faculty_id">Fakultas yang Dipimpin <span class="text-danger">*</span></label>
                                <select name="leader_faculty_id" id="leader_faculty_id" class="form-control" data-required="true">
                                    <option value="">Pilih Fakultas...</option>
                                    @foreach($faculties as $fac)
                                        <option value="{{ $fac->id }}" {{ old('leader_faculty_id', $ledFaculty->id ?? '') == $fac->id ? 'selected' : '' }}>{{ $fac->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Section: Supervisor Industri -->
                    <div id="section-supervisor" class="role-section d-none border-top pt-4 mt-4">
                        <h5 class="text-dark mb-4" style="font-weight: 700; color: var(--primary) !important;">Profil Supervisor Industri</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="industry_id">Mitra Industri / Perusahaan <span class="text-danger">*</span></label>
                                <select name="industry_id" id="industry_id" class="form-control" data-required="true">
                                    <option value="">Pilih Mitra Industri...</option>
                                    @foreach($industries as $ind)
                                        <option value="{{ $ind->id }}" {{ old('industry_id', $user->industrySupervisor->industry_id ?? '') == $ind->id ? 'selected' : '' }}>{{ $ind->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="supervisor_position">Jabatan di Perusahaan</label>
                                <input type="text" name="position" id="supervisor_position" class="form-control" placeholder="Contoh: HR Manager, Tech Lead" value="{{ old('position', $user->industrySupervisor->position ?? '') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-dark font-weight-bold" for="division">Divisi / Departemen</label>
                                <input type="text" name="division" id="division" class="form-control" placeholder="Contoh: Engineering, Human Resources" value="{{ old('division', $user->industrySupervisor->division ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end border-top pt-4 mt-5">
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary px-4 py-2" style="font-size: 13px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 py-2" style="font-size: 13px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const roleSelect = document.getElementById('role');
        const sections = {
            'mahasiswa': document.getElementById('section-mahasiswa'),
            'dpl': document.getElementById('section-dpl'),
            'kaprodi': document.getElementById('section-kaprodi'),
            'dekan': document.getElementById('section-dekan'),
            'supervisor-industri': document.getElementById('section-supervisor')
        };

        function toggleSections() {
            const selectedRole = roleSelect.value;
            Object.keys(sections).forEach(role => {
                const section = sections[role];
                if (section) {
                    if (role === selectedRole) {
                        section.classList.remove('d-none');
                        // Enable inputs inside and handle required attributes
                        section.querySelectorAll('input, select').forEach(el => {
                            el.disabled = false;
                            if (el.hasAttribute('data-required')) {
                                el.required = true;
                            }
                            if (el.tagName === 'SELECT') {
                                if (typeof jQuery !== 'undefined' && jQuery.fn.selectpicker) {
                                    try {
                                        jQuery(el).selectpicker('refresh');
                                    } catch(e) {}
                                }
                            }
                        });
                    } else {
                        section.classList.add('d-none');
                        // Disable inputs inside to exclude from form submission
                        section.querySelectorAll('input, select').forEach(el => {
                            el.disabled = true;
                            el.required = false;
                            if (el.tagName === 'SELECT') {
                                if (typeof jQuery !== 'undefined' && jQuery.fn.selectpicker) {
                                    try {
                                        jQuery(el).selectpicker('refresh');
                                    } catch(e) {}
                                }
                            }
                        });
                    }
                }
            });
        }

        roleSelect.addEventListener('change', toggleSections);
        toggleSections(); // run once on load
        
        // Also run toggleSections after a small delay to ensure any custom plugins have finished initializing
        setTimeout(toggleSections, 500);
    });
</script>
@endsection
