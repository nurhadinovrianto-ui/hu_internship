@extends('layouts.app')

@section('title', 'Detail Presensi Mahasiswa')

@section('styles')
    <!-- Leaflet CSS for Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <style>
        #map { height: 400px; border-radius: 12px; z-index: 1; }
        .leaflet-pane { z-index: 1; }
    </style>
@endsection

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Detail Presensi & Geofencing</h4>
            <p class="mb-0">Verifikasi lokasi dan foto kehadiran mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item"><a href="{{ route('industry.attendance.index') }}">Presensi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body text-center">
                <img src="{{ $attendance->student->user->avatar_url }}" class="rounded-circle mb-3" width="100" alt="Avatar">
                <h5 class="mb-1">{{ $attendance->student->user->name }}</h5>
                <p class="text-muted">{{ $attendance->student->nim }}</p>
                <hr>
                <ul class="list-group list-group-flush text-start">
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="mb-0">Tanggal</span><strong>{{ \Carbon\Carbon::parse($attendance->date)->format('d M Y') }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="mb-0">Check In</span><strong>{{ $attendance->check_in_time }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="mb-0">Check Out</span><strong>{{ $attendance->check_out_time ?? '-' }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="mb-0">Durasi</span><strong>{{ $attendance->work_duration_minutes ? round($attendance->work_duration_minutes / 60, 1) . ' Jam' : '-' }}</strong>
                    </li>
                    <li class="list-group-item px-0 d-flex justify-content-between">
                        <span class="mb-0">Status</span><span class="badge bg-success">Hadir</span>
                    </li>
                </ul>
            </div>
        </div>
        
        @if($attendance->check_in_photo)
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title">Foto Selfie Check-In</h5>
            </div>
            <div class="card-body text-center pt-3">
                <img src="{{ asset('storage/' . $attendance->check_in_photo) }}" class="img-fluid rounded" alt="Check In Photo">
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h5 class="card-title">Lokasi Check-In (Geofencing)</h5>
            </div>
            <div class="card-body">
                <div id="map"></div>
                
                @if($attendance->notes)
                <div class="mt-4 p-3 bg-light rounded">
                    <h6><strong>Catatan Mahasiswa:</strong></h6>
                    <p class="mb-0">{{ $attendance->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Map
        const checkInLat = {{ $attendance->check_in_lat }};
        const checkInLng = {{ $attendance->check_in_lng }};
        
        const industryLat = {{ $attendance->internship->vacancy->industry->latitude ?? 'null' }};
        const industryLng = {{ $attendance->internship->vacancy->industry->longitude ?? 'null' }};

        var map = L.map('map').setView([checkInLat, checkInLng], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Marker Check-In Mahasiswa
        var studentIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        L.marker([checkInLat, checkInLng], {icon: studentIcon}).addTo(map)
            .bindPopup('<b>Lokasi Check-In</b><br>Mahasiswa').openPopup();

        // Marker Lokasi Kantor (Jika ada data lat/lng)
        if(industryLat && industryLng) {
            var industryIcon = L.icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });

            L.marker([industryLat, industryLng], {icon: industryIcon}).addTo(map)
                .bindPopup('<b>Pusat Kantor</b><br>{{ $attendance->internship->vacancy->industry->name }}');
                
            // Draw Geofence Circle (100m)
            L.circle([industryLat, industryLng], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.2,
                radius: 100 // 100 meter
            }).addTo(map);
            
            // Auto fit bounds
            var group = new L.featureGroup([
                L.marker([checkInLat, checkInLng]), 
                L.marker([industryLat, industryLng])
            ]);
            map.fitBounds(group.getBounds().pad(0.5));
        }
    });
</script>
@endsection
