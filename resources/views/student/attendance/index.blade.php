@extends('layouts.app')

@section('title', 'Presensi Harian Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Presensi Harian Magang</h4>
            <p class="mb-0">Lakukan check-in masuk dan check-out pulang magang berbasis lokasi GPS.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Mahasiswa</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Presensi Harian</a></li>
        </ol>
    </div>
</div>

@if(isset($blocked) && $blocked)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center p-5">
                    <i class="la la-user-clock text-danger mb-4" style="font-size: 80px;"></i>
                    <h3 class="text-dark" style="font-weight: 700;">Akses Presensi Ditutup</h3>
                    <p class="text-muted mx-auto" style="max-width: 600px; font-size: 15px; line-height: 1.6;">
                        {{ $reason }}
                    </p>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-primary px-4 mt-3">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <!-- Input Presensi (Left Side) -->
        <div class="col-xl-5 col-lg-5 col-md-12 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <ul class="nav nav-pills mb-4 justify-content-center" id="attendanceTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold px-4" id="today-tab" data-bs-toggle="pill" data-bs-target="#today" type="button" role="tab" aria-controls="today" aria-selected="true">Hari Ini</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold px-4" id="backdate-tab" data-bs-toggle="pill" data-bs-target="#backdate" type="button" role="tab" aria-controls="backdate" aria-selected="false">Presensi Susulan</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="attendanceTabContent">
                        <!-- TAB HARI INI -->
                        <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
                            <h5 class="text-dark mb-4 text-center" style="font-weight: 700;">Panel Presensi Hari Ini</h5>
                            <h3 class="text-center text-primary mb-1" style="font-weight: 800;">{{ now()->format('H:i') }}</h3>
                            <p class="text-center text-muted mb-4">{{ now()->translatedFormat('l, d F Y') }}</p>

                            @if($geofenceEnabled)
                                <div id="gps-status" class="alert alert-info border-0 text-center py-2 mb-4" style="font-size: 13px;">
                                    <i class="la la-info-circle"></i> Mendapatkan koordinat GPS Anda...
                                </div>
                            @else
                                <div id="gps-status" class="alert alert-success border-0 text-center py-2 mb-4" style="font-size: 13px; background-color: #ECFDF5; color: #047857;">
                                    <i class="la la-unlock"></i> Pembatasan radius lokasi dimatikan (Bebas Radius). Anda dapat langsung melakukan absensi.
                                </div>
                            @endif

                            @if(!$todayAttendance)
                                <!-- Form Check In -->
                                <form action="{{ route('student.attendance.checkin') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="lat" id="lat-in">
                                    <input type="hidden" name="lng" id="lng-in">

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="location_type">Tujuan Kehadiran <span class="text-danger">*</span></label>
                                        <select name="location_type" id="location_type" class="form-control" required>
                                            <option value="industry">Ke Industri (Pabrik / Kantor)</option>
                                            <option value="campus">Ke Kampus / Bimbingan DPL</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label" for="photo">Foto Selfie di Lokasi Kehadiran <span class="text-danger">*</span></label>
                                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*" capture="user" required>
                                        <small class="text-muted">Gunakan kamera HP atau webcam secara langsung.</small>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label" for="notes">Keterangan / Rencana Kegiatan Hari Ini</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Tulis rencana aktivitas Anda..."></textarea>
                                    </div>

                                    <button type="submit" id="btn-checkin" class="btn btn-success text-white btn-block btn-lg py-3" style="font-weight: 700;" {{ !$geofenceEnabled ? '' : 'disabled' }}>
                                        <i class="la la-sign-in-alt me-1"></i> Check In Masuk Magang
                                    </button>
                                </form>
                            @elseif(!$todayAttendance->check_out_time)
                                <!-- Form Check Out -->
                                <div class="alert alert-success border-0 py-3 text-center mb-3" style="background-color: #ECFDF5; color: #047857;">
                                    <i class="la la-check-circle me-1"></i> Anda telah Check In pada pukul <strong>{{ Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</strong>.
                                </div>

                                <div class="p-3 mb-4 rounded bgl-success text-success border border-success" style="border-style: dashed !important;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="d-flex align-items-center">
                                            <span class="pulse-dot online" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #2bc155; margin-right: 6px;"></span>
                                            <strong style="font-size: 13px;">Pelacakan Real-time Aktif</strong>
                                        </div>
                                        <span class="badge light badge-success">Syncing</span>
                                    </div>
                                    <p class="text-muted mb-0" style="font-size: 12px;">Lokasi GPS Anda otomatis disinkronkan ke pembimbing magang. Pelacakan otomatis berhenti setelah Check Out.</p>
                                </div>

                                <form action="{{ route('student.attendance.checkout', $todayAttendance->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="lat" id="lat-out">
                                    <input type="hidden" name="lng" id="lng-out">

                                    <button type="submit" id="btn-checkout" class="btn btn-danger btn-block btn-lg py-3" style="font-weight: 700;" {{ !$geofenceEnabled ? '' : 'disabled' }}>
                                        <i class="la la-sign-out-alt me-1"></i> Check Out Pulang Magang
                                    </button>
                                </form>
                            @else
                                <!-- Selesai Absen Hari Ini -->
                                <div class="alert alert-info border-0 py-4 text-center mb-0" style="background-color: #F0F9FF; color: #0369A1;">
                                    <i class="la la-smile me-1" style="font-size: 32px; display: block; margin-bottom: 8px;"></i>
                                    <h5>Presensi Hari Ini Lengkap</h5>
                                    <p class="mb-0 mt-2" style="font-size: 13px;">Check In: {{ Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} WIB</p>
                                    <p class="mb-0" style="font-size: 13px;">Check Out: {{ Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }} WIB</p>
                                    <p class="mb-0 text-success font-weight-bold" style="font-size: 13px;">Durasi Kerja: {{ $todayAttendance->work_duration_formatted }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- TAB SUSULAN -->
                        <div class="tab-pane fade" id="backdate" role="tabpanel" aria-labelledby="backdate-tab">
                            <h5 class="text-dark mb-2 text-center" style="font-weight: 700;">Presensi Susulan</h5>
                            <p class="text-center text-muted mb-4" style="font-size: 13px;">Isi presensi untuk hari lampau yang terlewat.</p>
                            
                            <form action="{{ route('student.attendance.storeBackdate') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group mb-3">
                                    <label class="form-label" for="date">Tanggal Kehadiran <span class="text-danger">*</span></label>
                                    <input type="date" name="date" id="date" class="form-control" max="{{ now()->subDay()->toDateString() }}" required>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="check_in_time">Waktu Masuk <span class="text-danger">*</span></label>
                                            <input type="time" name="check_in_time" id="check_in_time" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label" for="check_out_time">Waktu Pulang <span class="text-danger">*</span></label>
                                            <input type="time" name="check_out_time" id="check_out_time" class="form-control" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="location_type_backdate">Tujuan Kehadiran <span class="text-danger">*</span></label>
                                    <select name="location_type" id="location_type_backdate" class="form-control" required>
                                        <option value="industry">Ke Industri (Pabrik / Kantor)</option>
                                        <option value="campus">Ke Kampus / Bimbingan DPL</option>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label" for="photo_backdate">Foto Bukti Kehadiran <span class="text-danger">*</span></label>
                                    <input type="file" name="photo" id="photo_backdate" class="form-control" accept="image/*" required>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label" for="notes_backdate">Keterangan / Alasan Susulan</label>
                                    <textarea name="notes" id="notes_backdate" class="form-control" rows="2" placeholder="Jelaskan aktivitas dan alasan Anda melakukan presensi susulan..."></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-block py-3" style="font-weight: 700;">
                                    <i class="la la-save me-1"></i> Simpan Presensi Susulan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Presensi (Right Side) -->
        <div class="col-xl-7 col-lg-7 col-md-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                    <h4 class="card-title" style="font-weight: 700;">Riwayat Kehadiran</h4>
                    <a href="{{ route('student.attendance.export') }}" class="btn btn-primary btn-sm"><i class="la la-download me-1"></i> Export</a>
                </div>
                <div class="card-body">
                    <form action="{{ url()->current() }}" method="GET" class="row g-2 mb-3 align-items-center">
                        <div class="col-sm-5">
                            <input type="month" name="month" class="form-control form-control-sm" value="{{ request('month') }}" title="Filter Bulan">
                        </div>
                        <div class="col-sm-4">
                            <select name="status" class="form-control form-control-sm" onchange="this.form.submit()">
                                <option value="">Semua Status</option>
                                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                                <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                                <option value="permit" {{ request('status') === 'permit' ? 'selected' : '' }}>Izin</option>
                                <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                                <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpa</option>
                            </select>
                        </div>
                        <div class="col-sm-3 d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1"><i class="la la-filter"></i></button>
                            @if(request()->hasAny(['month', 'status']))
                                <a href="{{ url()->current() }}" class="btn btn-light btn-sm" title="Reset"><i class="la la-undo"></i></a>
                            @endif
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-responsive-md table-hover">
                            <thead>
                                <tr>
                                    <th><strong>Tanggal</strong></th>
                                    <th><strong>Masuk</strong></th>
                                    <th><strong>Pulang</strong></th>
                                    <th><strong>Durasi</strong></th>
                                    <th><strong>Status</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendances as $att)
                                    <tr>
                                        <td>{{ $att->date->translatedFormat('d M Y') }}</td>
                                        <td>{{ $att->check_in_time ? Carbon\Carbon::parse($att->check_in_time)->format('H:i') : '-' }} WIB</td>
                                        <td>{{ $att->check_out_time ? Carbon\Carbon::parse($att->check_out_time)->format('H:i') : '-' }} WIB</td>
                                        <td>{{ $att->work_duration_formatted }}</td>
                                        <td>
                                            <span class="badge {{ $att->status_badge['class'] }}">
                                                {{ $att->status_badge['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada riwayat kehadiran.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($attendances->hasPages() || $attendances->total() > 0)
                        <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <small class="text-muted">Menampilkan {{ $attendances->firstItem() ?? 0 }} - {{ $attendances->lastItem() ?? 0 }} dari {{ $attendances->total() }} data</small>
                            {{ $attendances->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/browser-image-compression@2.0.2/dist/browser-image-compression.js"></script>
<script>
    async function handleImageCompression(e, form) {
        const fileInput = form.querySelector('input[type="file"][name="photo"]');
        if (!fileInput || fileInput.files.length === 0) return true;

        const file = fileInput.files[0];
        // If file is already smaller than 2MB, no need to compress
        if (file.size <= 2 * 1024 * 1024) return true;

        e.preventDefault();
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="la la-spinner la-spin me-1"></i> Memproses Gambar...';
        submitBtn.disabled = true;

        try {
            const options = {
                maxSizeMB: 1.8,
                maxWidthOrHeight: 1920,
                useWebWorker: true,
                fileType: 'image/jpeg', // Paksa format ke JPEG agar selalu kompatibel dengan validasi server
                initialQuality: 0.8 // Beri kompresi awal yang baik untuk kamera HP
            };
            const compressedFile = await imageCompression(file, options);
            
            // Beri nama file yang aman dan ekstensi yang benar (.jpeg)
            const safeFileName = file.name.replace(/\.[^/.]+$/, "") + ".jpeg";
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(new File([compressedFile], safeFileName, {
                type: 'image/jpeg',
            }));
            
            fileInput.files = dataTransfer.files;
            form.submit();
        } catch (error) {
            console.error("Image compression error:", error);
            alert("Gagal memproses gambar. Pastikan format gambar didukung.");
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
        return false;
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Intercept form submission for image compression
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            if (form.querySelector('input[type="file"][name="photo"]')) {
                form.addEventListener('submit', function(e) {
                    handleImageCompression(e, form);
                });
            }
        });


        const gpsStatus = document.getElementById("gps-status");
        const btnCheckin = document.getElementById("btn-checkin");
        const btnCheckout = document.getElementById("btn-checkout");
        const latIn = document.getElementById("lat-in");
        const lngIn = document.getElementById("lng-in");
        const latOut = document.getElementById("lat-out");
        const lngOut = document.getElementById("lng-out");

        const isGeofenceRequired = @json($geofenceEnabled ?? false);

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (latIn) {
                        latIn.value = lat;
                        lngIn.value = lng;
                    }
                    if (latOut) {
                        latOut.value = lat;
                        lngOut.value = lng;
                    }

                    gpsStatus.className = "alert alert-success border-0 text-center py-2 mb-4";
                    gpsStatus.innerHTML = `<i class="la la-check-circle"></i> Lokasi GPS Berhasil Didapatkan (${lat.toFixed(5)}, ${lng.toFixed(5)})`;
                    
                    if (btnCheckin) btnCheckin.removeAttribute("disabled");
                    if (btnCheckout) btnCheckout.removeAttribute("disabled");
                },
                function (error) {
                    if (isGeofenceRequired) {
                        gpsStatus.className = "alert alert-danger border-0 text-center py-2 mb-4";
                        switch(error.code) {
                            case error.PERMISSION_DENIED:
                                gpsStatus.innerHTML = `<i class="la la-times-circle"></i> Akses GPS ditolak. Aktifkan lokasi pada browser Anda.`;
                                break;
                            default:
                                gpsStatus.innerHTML = `<i class="la la-times-circle"></i> Gagal mendapatkan koordinat lokasi GPS.`;
                                break;
                        }
                    } else {
                        // Jika bebas radius, tetap izinkan absen meski GPS gagal
                        if (btnCheckin) btnCheckin.removeAttribute("disabled");
                        if (btnCheckout) btnCheckout.removeAttribute("disabled");
                    }
                },
                { enableHighAccuracy: true }
            );
        } else {
            if (isGeofenceRequired) {
                gpsStatus.className = "alert alert-danger border-0 text-center py-2 mb-4";
                gpsStatus.innerHTML = `<i class="la la-times-circle"></i> Geolocation tidak didukung oleh browser Anda.`;
            } else {
                if (btnCheckin) btnCheckin.removeAttribute("disabled");
                if (btnCheckout) btnCheckout.removeAttribute("disabled");
            }
        }
    });
</script>
@endsection
