@extends('layouts.app')

@section('title', 'Perbaiki Usulan Magang Mandiri')

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
            <h4>Perbaiki Usulan Magang Mandiri</h4>
            <p class="mb-0">Perbarui data atau dokumen usulan berdasarkan catatan revisi DPL / Kaprodi.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}">Mahasiswa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('student.self-proposals.index') }}">Magang Mandiri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Perbaiki</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <!-- Banner Catatan Revisi Jika Ada -->
        @if($proposal->dpl_notes && $proposal->dpl_status === 'revision')
            <div class="alert alert-warning mb-4 shadow-sm border-0">
                <h6 class="fw-bold mb-1"><i class="la la-user-tie me-1"></i> Catatan Revisi dari Dosen DPL ({{ $proposal->dpl?->user->name }}):</h6>
                <p class="mb-0">{{ $proposal->dpl_notes }}</p>
            </div>
        @endif

        @if($proposal->kaprodi_notes && $proposal->status === 'revision')
            <div class="alert alert-warning mb-4 shadow-sm border-0">
                <h6 class="fw-bold mb-1"><i class="la la-university me-1"></i> Catatan Revisi dari Kaprodi:</h6>
                <p class="mb-0">{{ $proposal->kaprodi_notes }}</p>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Form Pembaruan Usulan Magang</h4>
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

                <form action="{{ route('student.self-proposals.update', $proposal->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Identitas Perusahaan / Instansi</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Perusahaan / Lembaga <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $proposal->company_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Bidang Usaha / Sektor</label>
                            <input type="text" name="industry_sector" class="form-control" value="{{ old('industry_sector', $proposal->industry_sector) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Kantor / Lokasi Kerja <span class="text-danger">*</span></label>
                            <textarea name="company_address" class="form-control" rows="2" required>{{ old('company_address', $proposal->company_address) }}</textarea>
                        </div>
                        
                        <!-- Map Coordinate Picker for Geofence -->
                        <div class="col-12">
                            <label class="form-label fw-bold"><i class="la la-map-marker text-primary"></i> Titik Lokasi Kantor (Untuk Validasi Presensi & Geofence)</label>
                            <p class="text-muted" style="font-size: 12px;">Klik pada peta untuk menyesuaikan koordinat kantor magang Anda.</p>
                            <div id="map-picker" class="mb-2"></div>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="latitude" id="input-lat" class="form-control form-control-sm" value="{{ old('latitude', $proposal->latitude ?? '-6.2088') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="longitude" id="input-lng" class="form-control form-control-sm" value="{{ old('longitude', $proposal->longitude ?? '106.8456') }}" readonly>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">Radius</span>
                                        <input type="number" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', $proposal->geofence_radius ?? 500) }}" min="50" max="5000">
                                        <span class="input-group-text">Meter</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Narahubung / Mentor Pembimbing Industri</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Nama Narahubung / Mentor <span class="text-danger">*</span></label>
                            <input type="text" name="contact_person_name" class="form-control" value="{{ old('contact_person_name', $proposal->contact_person_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan Narahubung</label>
                            <input type="text" name="contact_person_position" class="form-control" value="{{ old('contact_person_position', $proposal->contact_person_position) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Narahubung / Mentor Mitra <span class="text-danger">*</span></label>
                            <input type="email" name="contact_person_email" class="form-control" value="{{ old('contact_person_email', $proposal->contact_person_email) }}" required>
                            <div class="form-text text-primary small">
                                <i class="la la-info-circle"></i> Email ini akan dibuatkan akun otomatis ketika usulan disetujui Kaprodi.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon / WhatsApp</label>
                            <input type="text" name="contact_person_phone" class="form-control" value="{{ old('contact_person_phone', $proposal->contact_person_phone) }}">
                        </div>
                    </div>

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">3. Rincian Posisi & Berkas Pendukung</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Posisi / Job Role Magang <span class="text-danger">*</span></label>
                            <input type="text" name="position_title" class="form-control" value="{{ old('position_title', $proposal->position_title) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $proposal->start_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $proposal->end_date->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Rencana Deskripsi Tugas / Jobdesk <span class="text-danger">*</span></label>
                            <textarea name="job_description" class="form-control" rows="3" required>{{ old('job_description', $proposal->job_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Unggah Bukti Penerimaan (Letter of Acceptance / LoA)</label>
                            @if($proposal->loa_file_path)
                                <div class="mb-2">
                                    <small class="text-muted">File saat ini: </small>
                                    <a href="{{ asset('storage/' . $proposal->loa_file_path) }}" target="_blank" class="badge badge-sm badge-info">
                                        <i class="la la-file-pdf"></i> Lihat LoA Tersimpan
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="loa_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah dokumen LoA yang sudah diunggah sebelumnya.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <a href="{{ route('student.self-proposals.show', $proposal->id) }}" class="btn btn-light">
                            <i class="la la-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="la la-save me-1"></i> Simpan & Ajukan Ulang Usulan
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
