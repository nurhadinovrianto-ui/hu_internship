@extends('layouts.app')

@section('title', 'Form Pengajuan Magang Mandiri')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    #map-picker {
        height: 320px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Form Usulan Magang Mandiri</h4>
            <p class="mb-0">Lengkapi data perusahaan mitra tempat Anda diterima magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.self-proposals.index') }}">Magang Mandiri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Ajukan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Data Pengajuan Magang Mandiri</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($assignedDpl)
                    <div class="alert alert-primary py-2 px-3 mb-4 d-flex align-items-center">
                        <i class="la la-user-tie fs-3 me-2 text-primary"></i>
                        <div>
                            <strong>Dosen DPL Pembimbing Anda:</strong> {{ $assignedDpl->user->name }} (NIDN: {{ $assignedDpl->nidn }}).
                            <br><small class="text-muted">Usulan magang mandiri ini akan dievaluasi relevansinya oleh Dosen DPL terlebih dahulu sebelum disahkan oleh Kaprodi.</small>
                        </div>
                    </div>
                @else
                    <div class="alert alert-secondary py-2 px-3 mb-4 d-flex align-items-center">
                        <i class="la la-info-circle fs-3 me-2 text-info"></i>
                        <div>
                            <strong>Informasi DPL:</strong> Belum ada DPL yang diplot untuk Anda pada semester ini. Usulan magang mandiri akan langsung diproses dan diplot DPL oleh Kaprodi.
                        </div>
                    </div>
                @endif

                <form action="{{ route('student.self-proposals.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Identitas Perusahaan / Instansi</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan / Lembaga <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" placeholder="Cth: PT Telekomunikasi Indonesia Tbk" value="{{ old('company_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bidang Usaha / Sektor</label>
                            <input type="text" name="industry_sector" class="form-control" placeholder="Cth: Teknologi Informasi / Perbankan / BUMN" value="{{ old('industry_sector') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Kantor / Lokasi Kerja <span class="text-danger">*</span></label>
                            <textarea name="company_address" class="form-control" rows="2" placeholder="Alamat lengkap lokasi kerja magang..." required>{{ old('company_address') }}</textarea>
                        </div>
                        
                        <!-- Map Coordinate Picker for Geofence -->
                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="la la-map-marker text-primary"></i> Titik Lokasi Kantor (Untuk Validasi Presensi & Geofence)</label>
                            <p class="text-muted" style="font-size: 12px;">Klik pada peta untuk menentukan titik lokasi kantor magang Anda.</p>
                            <div id="map-picker" class="mb-2"></div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="latitude" id="input-lat" class="form-control form-control-sm" placeholder="Latitude" value="{{ old('latitude', '-6.2088') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="longitude" id="input-lng" class="form-control form-control-sm" placeholder="Longitude" value="{{ old('longitude', '106.8456') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Radius</span>
                                        <input type="number" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', 500) }}" min="50" max="5000">
                                        <span class="input-group-text">Meter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Narahubung / Calon Pembimbing Lapangan Mitra</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Narahubung / HR / Mentor <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person_name" class="form-control" placeholder="Nama lengkap pembimbing industri" value="{{ old('contact_person_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan Narahubung</label>
                            <input type="text" name="contact_person_position" class="form-control" placeholder="Cth: Head of Engineering / HR Manager" value="{{ old('contact_person_position') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Narahubung / Mentor Mitra <span class="text-danger">*</span></label>
                            <input type="email" name="contact_person_email" class="form-control" placeholder="email@perusahaan.com" value="{{ old('contact_person_email') }}" required>
                            <div class="form-text text-primary small">
                                <i class="la la-info-circle"></i> <strong>Akun Otomatis:</strong> Email ini akan dibuatkan akun Supervisor Industri resmi secara otomatis begitu usulan disetujui Kaprodi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon / WhatsApp</label>
                            <input type="text" name="contact_person_phone" class="form-control" placeholder="08xxxxxxxxxx" value="{{ old('contact_person_phone') }}">
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Rincian Posisi & Berkas Pendukung</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Posisi / Job Role Magang <span class="text-danger">*</span></label>
                            <input type="text" name="position_title" class="form-control" placeholder="Cth: Junior Software Engineer Intern" value="{{ old('position_title') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Rencana Deskripsi Tugas / Jobdesk <span class="text-danger">*</span></label>
                            <textarea name="job_description" class="form-control" rows="3" placeholder="Jelaskan ruang lingkup pekerjaan yang akan Anda jalankan selama magang mandiri..." required>{{ old('job_description') }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Unggah Bukti Penerimaan (Letter of Acceptance / Surat Keterangan Diterima) <span class="text-danger">*</span></label>
                            <input type="file" name="loa_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format yang didukung: PDF, JPG, PNG (Maks. 5 MB).</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('student.self-proposals.index') }}" class="btn btn-light">
                            <i class="la la-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-paper-plane me-1"></i> Kirim Usulan Magang Mandiri
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const defaultLat = parseFloat(document.getElementById('input-lat').value) || -6.2088;
        const defaultLng = parseFloat(document.getElementById('input-lng').value) || 106.8456;

        const map = L.map('map-picker').setView([defaultLat, defaultLng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('input-lat').value = lat.toFixed(7);
            document.getElementById('input-lng').value = lng.toFixed(7);
        }

        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endsection
