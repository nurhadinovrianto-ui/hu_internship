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
                        <div class="alert alert-success border-0 py-3 text-center mb-4" style="background-color: #ECFDF5; color: #047857;">
                            <i class="la la-check-circle me-1"></i> Anda telah Check In pada pukul <strong>{{ Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</strong>.
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

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $attendances->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
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
