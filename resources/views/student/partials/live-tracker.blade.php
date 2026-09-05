@if(auth()->check() && auth()->user()->hasRole('mahasiswa'))
@php
    $trackerStudent = auth()->user()->student;
    $trackerInternship = $trackerStudent ? $trackerStudent->internships()->where('status', \App\Models\Internship::STATUS_ACTIVE)->first() : null;
    $isTrackingSessionActive = $trackerInternship ? $trackerInternship->attendances()->where('date', now()->toDateString())->whereNotNull('check_in_time')->whereNull('check_out_time')->exists() : false;
@endphp

@if($isTrackingSessionActive)
<div id="student-live-tracker-widget" class="student-tracker-floating shadow" style="
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 99998;
    background: #ffffff;
    border: 1px solid rgba(43, 193, 85, 0.3);
    border-radius: 30px;
    padding: 7px 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    font-family: 'Poppins', sans-serif;
    font-size: 12px;
    font-weight: 500;
    color: #333333;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
">
    <span class="pulse-dot online" style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #2bc155; box-shadow: 0 0 0 0 rgba(43, 193, 85, 0.7); animation: tracker-pulse 1.6s infinite;"></span>
    <span id="tracker-widget-label">GPS Magang Aktif</span>
    <span class="badge light badge-success" id="tracker-widget-time" style="font-size: 10px; padding: 2px 6px;">Sync</span>
</div>

<style>
    @keyframes tracker-pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(43, 193, 85, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(43, 193, 85, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(43, 193, 85, 0); }
    }
    [data-theme-version="dark"] #student-live-tracker-widget {
        background: #1e1e2d !important;
        color: #fff !important;
        border-color: rgba(43, 193, 85, 0.4) !important;
    }
</style>

<script>
(function() {
    let pingInterval = null;
    let isPinging = false;
    const PING_DELAY_MS = 30000; // 30 Detik

    function sendLocationPing() {
        if (!navigator.geolocation || isPinging) return;

        isPinging = true;
        navigator.geolocation.getCurrentPosition(
            async function(position) {
                let batteryLevel = null;
                try {
                    if (navigator.getBattery) {
                        const battery = await navigator.getBattery();
                        batteryLevel = Math.round(battery.level * 100);
                    }
                } catch (e) {}

                const payload = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    speed: position.coords.speed,
                    heading: position.coords.heading,
                    battery_level: batteryLevel
                };

                fetch("{{ route('student.tracking.ping') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => {
                    const timeBadge = document.getElementById('tracker-widget-time');
                    if (timeBadge) {
                        const now = new Date();
                        timeBadge.innerText = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    }
                })
                .catch(err => {
                    console.debug('Tracking ping failed:', err);
                })
                .finally(() => {
                    isPinging = false;
                });
            },
            function(err) {
                isPinging = false;
                console.debug('GPS error:', err.message);
                const label = document.getElementById('tracker-widget-label');
                if (label && err.code === 1) {
                    label.innerText = 'GPS Ditolak';
                }
            },
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 10000
            }
        );
    }

    // Jalankan pertama kali setelah 2 detik halaman dimuat
    setTimeout(sendLocationPing, 2000);
    // Jalankan rutin tiap 30 detik
    pingInterval = setInterval(sendLocationPing, PING_DELAY_MS);
})();
</script>
@endif
@endif
