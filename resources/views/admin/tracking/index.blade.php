@extends('layouts.app')

@section('title', 'Lacak Mahasiswa Realtime')

@section('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<style>
    /* Edumin Card & Map Container */
    .tracking-map-container {
        position: relative;
        height: 640px;
        width: 100%;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(0, 0, 0, 0.08);
    }

    [data-theme-version="dark"] .tracking-map-container {
        border-color: rgba(255, 255, 255, 0.08);
    }

    #tracking-map {
        height: 100%;
        width: 100%;
        z-index: 1;
    }

    /* Custom Leaflet Markers matching Edumin */
    .student-marker-wrapper {
        position: relative;
        width: 44px;
        height: 44px;
    }

    .student-marker-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        object-fit: cover;
        background: #fff;
        position: relative;
        z-index: 2;
    }

    .student-marker-avatar.online-inside {
        border-color: #2bc155; /* Edumin Success */
    }

    .student-marker-avatar.online-outside {
        border-color: #ff9f43; /* Edumin Warning */
    }

    .student-marker-avatar.offline {
        border-color: #b5b5c3;
        filter: grayscale(80%);
    }

    /* Pulsing radar effect */
    .marker-pulse {
        position: absolute;
        top: -4px;
        left: -4px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        z-index: 1;
        animation: radar-pulse 2s infinite;
        opacity: 0;
    }

    .marker-pulse.online-inside {
        background: rgba(43, 193, 85, 0.45);
    }

    .marker-pulse.online-outside {
        background: rgba(255, 159, 67, 0.45);
    }

    @keyframes radar-pulse {
        0% { transform: scale(0.6); opacity: 0.8; }
        70% { transform: scale(1.6); opacity: 0; }
        100% { transform: scale(1.8); opacity: 0; }
    }

    /* Industry Marker */
    .industry-marker-pin {
        background: #6a42c2; /* Edumin Primary */
        color: #fff;
        width: 34px;
        height: 34px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        border: 2px solid #fff;
    }

    .industry-marker-pin i {
        transform: rotate(45deg);
        font-size: 16px;
    }

    /* Floating map control bar in Edumin style */
    .map-floating-panel {
        position: absolute;
        top: 15px;
        left: 60px;
        z-index: 1000;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    [data-theme-version="dark"] .map-floating-panel {
        background: rgba(30, 30, 45, 0.95) !important;
        color: #fff !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }

    /* Student List in Sidebar */
    .student-track-list {
        max-height: 520px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .student-track-list::-webkit-scrollbar {
        width: 4px;
    }

    .student-track-list::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 4px;
    }

    [data-theme-version="dark"] .student-track-list::-webkit-scrollbar-thumb {
        background: #3f3f5a;
    }

    .student-track-card {
        padding: 12px 14px;
        border-radius: 10px;
        background: #f8fafc;
        margin-bottom: 10px;
        border: 1px solid rgba(0, 0, 0, 0.04);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    [data-theme-version="dark"] .student-track-card {
        background: #252538 !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }

    .student-track-card:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }

    [data-theme-version="dark"] .student-track-card:hover {
        background: #2b2b40 !important;
    }

    .student-track-card.active-selected {
        border-color: #6a42c2 !important;
        background: #f4f0fd !important;
    }

    [data-theme-version="dark"] .student-track-card.active-selected {
        background: #2f2549 !important;
        border-color: #8b68db !important;
    }

    .pulse-dot {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }

    .pulse-dot.online {
        background-color: #2bc155;
        box-shadow: 0 0 0 0 rgba(43, 193, 85, 0.7);
        animation: dot-pulse 1.6s infinite;
    }

    .pulse-dot.warning {
        background-color: #ff9f43;
        box-shadow: 0 0 0 0 rgba(255, 159, 67, 0.7);
        animation: dot-pulse 1.6s infinite;
    }

    .pulse-dot.offline {
        background-color: #b5b5c3;
    }

    @keyframes dot-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(43, 193, 85, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(43, 193, 85, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(43, 193, 85, 0); }
    }

    /* Custom Leaflet Popup conforming to Edumin */
    .leaflet-popup-content-wrapper {
        border-radius: 12px !important;
        padding: 4px !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
        font-family: 'Poppins', sans-serif !important;
    }

    [data-theme-version="dark"] .leaflet-popup-content-wrapper,
    [data-theme-version="dark"] .leaflet-popup-tip {
        background: #1e1e2d !important;
        color: #fff !important;
    }

    .leaflet-popup-content {
        margin: 12px 14px !important;
        line-height: 1.4 !important;
    }
</style>
@endsection

@section('content')
<!-- EDUMIN PAGE TITLE & BREADCRUMB -->
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Lacak Mahasiswa Realtime</h4>
            <p class="mb-0">Pusat pemantauan koordinat GPS dan kepatuhan area magang mahasiswa</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Lacak Mahasiswa</a></li>
        </ol>
    </div>
</div>

<!-- EDUMIN SIGNATURE STAT CARDS (Matched 1:1 with Admin Dashboard) -->
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-users" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Magang</p>
                        <h3 class="text-white mb-0 fw-bold" id="stat-total">0</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Mahasiswa Aktif</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-satellite-dish" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Sedang Online</p>
                        <h3 class="text-white mb-0 fw-bold" id="stat-online">0</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">GPS Aktif (&lt; 2 menit)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-shield-alt" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Di Area Kantor</p>
                        <h3 class="text-white mb-0 fw-bold" id="stat-inside">0</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Dalam Radius Geofence</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning rounded-circle" style="width: 54px; height: 54px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="la la-exclamation-triangle" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Di Luar Kantor</p>
                        <h3 class="text-white mb-0 fw-bold" id="stat-outside">0</h3>
                        <small class="text-white opacity-75" style="font-size: 11px;">Luar Radius Geofence</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT ROW: MAP & SIDEBAR INSIDE EDUMIN CARDS -->
<div class="row">
    <!-- LEFT: MAP CARD -->
    <div class="col-xl-8 col-lg-7 col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="card-title">Peta Persebaran Lokasi Mahasiswa</h4>
                    <p class="mb-0 text-muted" style="font-size: 12px;" id="active-marker-count">Memuat lokasi...</p>
                </div>

                <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                    <span class="badge light badge-success px-3 py-2 d-flex align-items-center">
                        <span class="pulse-dot online"></span>
                        <span id="live-indicator-text">Live Monitoring</span>
                    </span>

                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" id="btn-refresh-interval">
                            <i class="la la-clock me-1"></i> <span id="current-interval-label">10s</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item interval-option" href="javascript:void(0)" data-interval="5">Refresh Tiap 5 Detik</a></li>
                            <li><a class="dropdown-item interval-option active" href="javascript:void(0)" data-interval="10">Refresh Tiap 10 Detik</a></li>
                            <li><a class="dropdown-item interval-option" href="javascript:void(0)" data-interval="30">Refresh Tiap 30 Detik</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item interval-option text-danger" href="javascript:void(0)" data-interval="0">Jeda (Manual Refresh)</a></li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-primary btn-sm" id="btn-manual-refresh" title="Segarkan Data Sekarang">
                        <i class="la la-sync-alt" id="icon-refresh"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-3">
                <div class="tracking-map-container">
                    <!-- Floating Indicator on Map -->
                    <div class="map-floating-panel">
                        <i class="la la-compass text-primary" style="font-size: 16px;"></i>
                        <span id="map-status-pill">Koordinat Terkini</span>
                        <div class="vr mx-1"></div>
                        <span class="text-muted fw-normal" id="last-update-time">Update: Baru saja</span>
                    </div>

                    <!-- Map -->
                    <div id="tracking-map"></div>
                </div>

                <!-- Route Polyline Active Notification Bar -->
                <div id="route-active-bar" class="alert alert-info border-0 mt-3 d-none align-items-center justify-content-between p-3" style="border-radius: 10px;">
                    <div class="d-flex align-items-center">
                        <i class="la la-route text-primary me-3" style="font-size: 24px;"></i>
                        <div>
                            <h6 class="mb-0 text-dark fw-bold">Menampilkan Rute Hari Ini: <span id="route-student-name">-</span></h6>
                            <small class="text-muted"><span id="route-points-count">0</span> titik koordinat terdeteksi hari ini.</small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btn-close-route">
                        <i class="la la-times me-1"></i> Tutup Jalur Rute
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: FILTER & STUDENT LIST CARD -->
    <div class="col-xl-4 col-lg-5 col-md-12 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 pb-0 d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Mahasiswa</h4>
                <span class="badge light badge-primary" id="sidebar-count-badge">0 Orang</span>
            </div>

            <div class="card-body pt-3">
                <!-- Search Box -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="la la-search text-muted"></i></span>
                        <input type="text" id="filter-search" class="form-control bg-light border-0" placeholder="Cari nama atau NIM...">
                    </div>
                </div>

                <!-- Select Filters -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <select id="filter-prodi" class="form-control form-select form-select-sm">
                            <option value="">Semua Prodi</option>
                            @foreach($studyPrograms as $prodi)
                                <option value="{{ $prodi->id }}">{{ $prodi->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <select id="filter-industry" class="form-control form-select form-select-sm">
                            <option value="">Semua Mitra</option>
                            @foreach($industries as $ind)
                                <option value="{{ $ind->id }}">{{ \Illuminate\Support\Str::limit($ind->name, 16) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Status Filter Pills (Edumin Style) -->
                <div class="d-flex gap-1 mb-3 overflow-auto pb-1" id="status-pills">
                    <button class="btn btn-xs btn-primary status-pill active" data-status="">Semua</button>
                    <button class="btn btn-xs light btn-success status-pill" data-status="inside_geofence">Di Area</button>
                    <button class="btn btn-xs light btn-warning status-pill" data-status="outside_geofence">Luar Area</button>
                    <button class="btn btn-xs light btn-secondary status-pill" data-status="offline">Offline</button>
                </div>

                <!-- Student List Container -->
                <div class="student-track-list" id="student-list-container">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-primary mb-2" role="status"></div>
                        <p class="mb-0" style="font-size: 13px;">Sedang mengambil data mahasiswa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Peta Leaflet
    const defaultCenter = [-6.2088, 106.8456];
    const defaultZoom = 13;

    // Deteksi Theme Version Edumin
    const isDarkMode = document.body.getAttribute('data-theme-version') === 'dark';

    // Base Layers
    const cartoLight = L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; CARTO'
    });

    const cartoDark = L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        attribution: '&copy; CARTO'
    });

    const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        maxZoom: 18,
        attribution: 'Tiles &copy; Esri'
    });

    const defaultLayer = isDarkMode ? cartoDark : cartoLight;

    const map = L.map('tracking-map', {
        center: defaultCenter,
        zoom: defaultZoom,
        layers: [defaultLayer]
    });

    const baseMaps = {
        "Peta Terang (Clean)": cartoLight,
        "Peta Gelap (Dark)": cartoDark,
        "Peta Standar (OSM)": osmLayer,
        "Satelit (Esri)": satelliteLayer
    };
    L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

    // Layer Groups
    const studentMarkersLayer = L.layerGroup().addTo(map);
    const industryMarkersLayer = L.layerGroup().addTo(map);
    let routePolyline = null;
    let routeStartMarker = null;
    let routeEndMarker = null;

    // State Variables
    let studentsCache = [];
    let activeStudentId = null;
    let refreshTimer = null;
    let refreshIntervalSeconds = 10;
    let isFetching = false;

    // 2. Fetch Live Tracking Data
    function fetchLiveData() {
        if (isFetching) return;
        isFetching = true;

        const iconRefresh = document.getElementById('icon-refresh');
        if (iconRefresh) iconRefresh.classList.add('fa-spin');

        const params = new URLSearchParams();
        const prodi = document.getElementById('filter-prodi')?.value;
        const industry = document.getElementById('filter-industry')?.value;
        const search = document.getElementById('filter-search')?.value;
        const activePill = document.querySelector('.status-pill.active');
        const status = activePill ? activePill.getAttribute('data-status') : '';

        if (prodi) params.append('study_program_id', prodi);
        if (industry) params.append('industry_id', industry);
        if (search) params.append('search', search);
        if (status) params.append('status', status);

        fetch(`{{ route('admin.tracking.live-data') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                studentsCache = data.students || [];
                updateMetrics(data.metrics);
                renderMapMarkers(studentsCache);
                renderStudentList(studentsCache);

                const now = new Date();
                const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                document.getElementById('last-update-time').innerText = `Update: ${timeStr}`;
                document.getElementById('active-marker-count').innerText = `${studentsCache.filter(s => s.location && s.location.latitude).length} dari ${studentsCache.length} mahasiswa berposisi GPS aktif`;
            }
        })
        .catch(err => {
            console.error('Gagal mengambil data live tracking:', err);
        })
        .finally(() => {
            isFetching = false;
            if (iconRefresh) iconRefresh.classList.remove('fa-spin');
        });
    }

    // 3. Update Metrik Ringkasan
    function updateMetrics(metrics) {
        if (!metrics) return;
        document.getElementById('stat-total').innerText = metrics.total ?? 0;
        document.getElementById('stat-online').innerText = metrics.online ?? 0;
        document.getElementById('stat-inside').innerText = metrics.inside_geofence ?? 0;
        document.getElementById('stat-outside').innerText = metrics.outside_geofence ?? 0;
    }

    // 4. Render Markers di Peta Leaflet
    const studentMarkerMap = new Map();
    const industryIdsRendered = new Set();

    function renderMapMarkers(students) {
        studentMarkersLayer.clearLayers();
        industryMarkersLayer.clearLayers();
        studentMarkerMap.clear();
        industryIdsRendered.clear();

        const latLngsToFit = [];

        students.forEach(student => {
            // Render Lokasi Industri & Geofence
            const ind = student.industry;
            if (ind && ind.latitude && ind.longitude && !industryIdsRendered.has(ind.id)) {
                industryIdsRendered.add(ind.id);

                const indIcon = L.divIcon({
                    className: 'custom-industry-icon',
                    html: `<div class="industry-marker-pin"><i class="la la-building"></i></div>`,
                    iconSize: [34, 34],
                    iconAnchor: [17, 34],
                    popupAnchor: [0, -34]
                });

                const indMarker = L.marker([ind.latitude, ind.longitude], { icon: indIcon })
                    .bindPopup(`
                        <div style="font-size: 13px;">
                            <span class="badge light badge-primary mb-1">Perusahaan Mitra</span>
                            <h6 class="fw-bold mb-1">${ind.name}</h6>
                            <p class="text-muted mb-1" style="font-size: 11px;"><i class="la la-map-marker text-primary"></i> ${ind.address || 'Alamat kantor'}</p>
                            <small class="text-info"><i class="la la-bullseye"></i> Radius Geofence: ${ind.geofence_radius || 500} meter</small>
                        </div>
                    `);
                industryMarkersLayer.addLayer(indMarker);
                latLngsToFit.push([ind.latitude, ind.longitude]);

                const geofenceCircle = L.circle([ind.latitude, ind.longitude], {
                    radius: ind.geofence_radius || 500,
                    color: '#6a42c2',
                    weight: 1.5,
                    fillColor: '#6a42c2',
                    fillOpacity: 0.1,
                    dashArray: '5, 6'
                });
                industryMarkersLayer.addLayer(geofenceCircle);
            }

            // Render Posisi Mahasiswa
            const loc = student.location;
            if (loc && loc.latitude && loc.longitude) {
                const isOnline = loc.is_online;
                const isInside = student.is_inside_geofence;

                let statusClass = 'offline';
                let statusBadge = '<span class="badge light badge-secondary">Offline</span>';

                if (isOnline) {
                    if (isInside) {
                        statusClass = 'online-inside';
                        statusBadge = '<span class="badge light badge-success">Di Area Kantor</span>';
                    } else {
                        statusClass = 'online-outside';
                        statusBadge = '<span class="badge light badge-warning">Di Luar Area</span>';
                    }
                }

                const markerHtml = `
                    <div class="student-marker-wrapper">
                        ${isOnline ? `<div class="marker-pulse ${statusClass}"></div>` : ''}
                        <img src="${student.photo}" class="student-marker-avatar ${statusClass}" alt="${student.name}">
                    </div>
                `;

                const studentIcon = L.divIcon({
                    className: 'custom-student-icon',
                    html: markerHtml,
                    iconSize: [44, 44],
                    iconAnchor: [22, 22],
                    popupAnchor: [0, -22]
                });

                const distanceText = student.distance_to_industry !== null 
                    ? (student.distance_to_industry > 1000 
                        ? `${(student.distance_to_industry / 1000).toFixed(1)} km dari kantor` 
                        : `${student.distance_to_industry} meter dari kantor`)
                    : 'Jarak belum dihitung';

                const batteryText = loc.battery_level !== null 
                    ? `<span class="me-2"><i class="la la-battery-three-quarters text-success"></i> ${loc.battery_level}%</span>` 
                    : '';

                const speedText = loc.speed !== null && loc.speed > 0
                    ? `<span class="me-2"><i class="la la-tachometer-alt text-primary"></i> ${Math.round(loc.speed * 3.6)} km/h</span>`
                    : '';

                const popupContent = `
                    <div style="min-width: 250px; font-size: 13px;">
                        <div class="d-flex align-items-center mb-2">
                            <img src="${student.photo}" class="rounded-circle me-2" style="width: 42px; height: 42px; object-fit: cover;">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">${student.name}</h6>
                                <small class="text-muted">${student.nim} &bull; ${student.study_program}</small>
                            </div>
                        </div>
                        <div class="mb-2">
                            ${statusBadge}
                            <span class="badge light badge-primary ms-1">${student.industry.name}</span>
                        </div>
                        <div class="p-2 bg-light rounded mb-2" style="font-size: 12px;">
                            <div class="mb-1"><i class="la la-map-pin text-primary me-1"></i> <strong>${distanceText}</strong></div>
                            <div class="mb-1 text-muted"><i class="la la-clock me-1"></i> Terakhir aktif: ${loc.last_seen}</div>
                            <div class="d-flex align-items-center text-muted" style="font-size: 11px;">
                                ${batteryText}
                                ${speedText}
                                <span><i class="la la-crosshairs text-muted"></i> Akurasi: &plusmn;${Math.round(loc.accuracy || 0)}m</span>
                            </div>
                        </div>
                        <div class="d-grid gap-1">
                            <button type="button" class="btn btn-primary btn-xs btn-view-route" data-student-id="${student.id}" data-student-name="${student.name}">
                                <i class="la la-route me-1"></i> Lihat Rute Hari Ini
                            </button>
                        </div>
                    </div>
                `;

                const marker = L.marker([loc.latitude, loc.longitude], { icon: studentIcon })
                    .bindPopup(popupContent);

                studentMarkersLayer.addLayer(marker);
                studentMarkerMap.set(student.id, marker);
                latLngsToFit.push([loc.latitude, loc.longitude]);
            }
        });

        map.on('popupopen', function () {
            const btn = document.querySelector('.btn-view-route');
            if (btn) {
                btn.onclick = function () {
                    const sid = this.getAttribute('data-student-id');
                    const sname = this.getAttribute('data-student-name');
                    loadStudentRoute(sid, sname);
                };
            }
        });
    }

    // 5. Render Daftar Mahasiswa di Sidebar Panel
    function renderStudentList(students) {
        const container = document.getElementById('student-list-container');
        document.getElementById('sidebar-count-badge').innerText = `${students.length} Orang`;

        if (students.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="la la-user-slash mb-2" style="font-size: 32px;"></i>
                    <p class="mb-0" style="font-size: 13px;">Tidak ada mahasiswa sesuai filter.</p>
                </div>
            `;
            return;
        }

        let html = '';
        students.forEach(student => {
            const loc = student.location;
            const isOnline = loc && loc.is_online;
            const isInside = student.is_inside_geofence;

            let pulseClass = 'offline';
            let statusText = 'Offline';
            let statusBadge = 'badge light badge-secondary';

            if (isOnline) {
                if (isInside) {
                    pulseClass = 'online';
                    statusText = 'Di Area Magang';
                    statusBadge = 'badge light badge-success';
                } else {
                    pulseClass = 'warning';
                    statusText = 'Di Luar Area';
                    statusBadge = 'badge light badge-warning';
                }
            }

            const isSelected = (student.id === activeStudentId) ? 'active-selected' : '';
            const distance = student.distance_to_industry !== null 
                ? (student.distance_to_industry > 1000 ? `${(student.distance_to_industry / 1000).toFixed(1)} km` : `${student.distance_to_industry} m`) 
                : '-';

            html += `
                <div class="student-track-card ${isSelected}" data-student-id="${student.id}" id="card-student-${student.id}">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <div class="d-flex align-items-center">
                            <span class="pulse-dot ${pulseClass}"></span>
                            <span class="${statusBadge} px-2 py-1" style="font-size: 11px;">${statusText}</span>
                        </div>
                        <small class="text-muted" style="font-size: 11px;">${loc && loc.last_seen ? loc.last_seen : '-'}</small>
                    </div>

                    <div class="d-flex align-items-center mt-2">
                        <img src="${student.photo}" class="rounded-circle me-2" style="width: 38px; height: 38px; object-fit: cover;" alt="${student.name}">
                        <div class="overflow-hidden">
                            <h6 class="mb-0 text-dark fw-bold text-truncate" style="font-size: 13px;">${student.name}</h6>
                            <p class="mb-0 text-muted" style="font-size: 11px;">${student.nim} &bull; ${student.study_program}</p>
                            <p class="mb-0 text-primary fw-medium text-truncate" style="font-size: 11px;"><i class="la la-building"></i> ${student.industry.name} (${distance})</p>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;

        container.querySelectorAll('.student-track-card').forEach(card => {
            card.addEventListener('click', function () {
                const sid = parseInt(this.getAttribute('data-student-id'));
                focusStudent(sid);
            });
        });
    }

    // 6. Fokuskan Peta ke Mahasiswa Terpilih
    function focusStudent(studentId) {
        activeStudentId = studentId;

        document.querySelectorAll('.student-track-card').forEach(c => c.classList.remove('active-selected'));
        const card = document.getElementById(`card-student-${studentId}`);
        if (card) card.classList.add('active-selected');

        const marker = studentMarkerMap.get(studentId);
        if (marker) {
            map.flyTo(marker.getLatLng(), 17, { duration: 1.2 });
            setTimeout(() => {
                marker.openPopup();
            }, 1200);
        } else {
            toastr.info('Mahasiswa ini belum memiliki titik koordinat GPS.');
        }
    }

    // 7. Render Jejak Rute Hari Ini (Polyline)
    function loadStudentRoute(studentId, studentName) {
        toastr.info('Memuat rute perjalanan...');

        fetch(`{{ url('admin/tracking') }}/${studentId}/history`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success || data.points.length === 0) {
                toastr.warning('Belum ada jejak rute tercatat hari ini.');
                return;
            }

            clearRoute();

            const latLngs = data.points.map(p => [p.lat, p.lng]);

            routePolyline = L.polyline(latLngs, {
                color: '#6a42c2',
                weight: 5,
                opacity: 0.85,
                lineJoin: 'round',
                dashArray: '2, 8',
                dashOffset: '0'
            }).addTo(map);

            const startPoint = latLngs[0];
            const endPoint = latLngs[latLngs.length - 1];

            routeStartMarker = L.circleMarker(startPoint, {
                radius: 8,
                fillColor: '#2bc155',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Titik Awal:</b> ${data.points[0].time}`).addTo(map);

            routeEndMarker = L.circleMarker(endPoint, {
                radius: 8,
                fillColor: '#f3505c',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).bindPopup(`<b>Titik Terakhir:</b> ${data.points[data.points.length - 1].time}`).addTo(map);

            map.fitBounds(routePolyline.getBounds(), { padding: [40, 40] });

            document.getElementById('route-student-name').innerText = studentName;
            document.getElementById('route-points-count').innerText = data.points.length;
            const bar = document.getElementById('route-active-bar');
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');

            toastr.success(`Jalur rute ${studentName} ditampilkan (${data.points.length} titik).`);
        })
        .catch(err => {
            console.error('Gagal mengambil rute:', err);
            toastr.error('Terjadi kesalahan saat memuat rute.');
        });
    }

    function clearRoute() {
        if (routePolyline) {
            map.removeLayer(routePolyline);
            routePolyline = null;
        }
        if (routeStartMarker) {
            map.removeLayer(routeStartMarker);
            routeStartMarker = null;
        }
        if (routeEndMarker) {
            map.removeLayer(routeEndMarker);
            routeEndMarker = null;
        }
        const bar = document.getElementById('route-active-bar');
        bar.classList.add('d-none');
        bar.classList.remove('d-flex');
    }

    document.getElementById('btn-close-route')?.addEventListener('click', clearRoute);

    // 8. Event Handlers & Filter Edumin
    document.getElementById('filter-prodi')?.addEventListener('change', fetchLiveData);
    document.getElementById('filter-industry')?.addEventListener('change', fetchLiveData);
    
    let searchDebounce = null;
    document.getElementById('filter-search')?.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(fetchLiveData, 400);
    });

    // Filter status pills (Edumin buttons)
    document.querySelectorAll('.status-pill').forEach(pill => {
        pill.addEventListener('click', function () {
            document.querySelectorAll('.status-pill').forEach(p => {
                p.classList.remove('active');
                p.classList.remove('btn-primary');
                const st = p.getAttribute('data-status');
                if (st === 'inside_geofence') p.className = 'btn btn-xs light btn-success status-pill';
                else if (st === 'outside_geofence') p.className = 'btn btn-xs light btn-warning status-pill';
                else if (st === 'offline') p.className = 'btn btn-xs light btn-secondary status-pill';
                else p.className = 'btn btn-xs light btn-primary status-pill';
            });

            this.classList.add('active');
            this.classList.remove('light');
            this.classList.add('btn-primary');
            fetchLiveData();
        });
    });

    document.getElementById('btn-manual-refresh')?.addEventListener('click', fetchLiveData);

    document.querySelectorAll('.interval-option').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.interval-option').forEach(o => o.classList.remove('active'));
            this.classList.add('active');

            const val = parseInt(this.getAttribute('data-interval'));
            refreshIntervalSeconds = val;

            const labelSpan = document.getElementById('current-interval-label');
            const indText = document.getElementById('live-indicator-text');

            if (val === 0) {
                labelSpan.innerText = 'Jeda';
                indText.innerText = 'Monitoring Dijeda';
                clearInterval(refreshTimer);
                refreshTimer = null;
            } else {
                labelSpan.innerText = `${val}s`;
                indText.innerText = 'Live Monitoring';
                startAutoRefresh();
                fetchLiveData();
            }
        });
    });

    function startAutoRefresh() {
        if (refreshTimer) clearInterval(refreshTimer);
        if (refreshIntervalSeconds > 0) {
            refreshTimer = setInterval(fetchLiveData, refreshIntervalSeconds * 1000);
        }
    }

    // 9. Load Pertama Kali
    fetchLiveData();
    startAutoRefresh();
});
</script>
@endsection
