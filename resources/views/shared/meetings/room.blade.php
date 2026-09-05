@extends('layouts.app')

@section('content')
<div class="container-fluid h-100">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4>Ruang Meeting: {{ $meeting->topic }}</h4>
                <p class="mb-0">Bersama: {{ $meeting->host?->name ?? 'Host' }}</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Meeting</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Room</a></li>
            </ol>
        </div>
    </div>

    <!-- Info Banner & Direct Link Action to Bypass 5-Minute Iframe Restriction -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="alert alert-info alert-dismissible fade show d-flex flex-column flex-md-row align-items-md-center justify-content-between shadow-sm" role="alert">
                <div class="mb-2 mb-md-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Tips Meeting Tanpa Batas Waktu:</strong> Server publik Jitsi membatasi sesi embed dalam halaman maksimal 5 menit. Untuk meeting <strong>tanpa batas waktu (unlimited)</strong>, silakan buka langsung di tab baru.
                </div>
                <div class="d-flex gap-2">
                    @php
                        $jitsiDomain = \App\Models\Setting::getValue('jitsi_domain', 'meet.jit.si');
                        $roomUrl = "https://{$jitsiDomain}/simang_{$meeting->jitsi_room_id}";
                    @endphp
                    <a href="{{ $roomUrl }}" target="_blank" class="btn btn-primary btn-sm text-nowrap shadow-sm">
                        <i class="fas fa-external-link-alt me-1"></i> Buka di Tab Baru (Unlimited)
                    </a>
                    <button type="button" class="btn btn-outline-primary btn-sm text-nowrap" onclick="navigator.clipboard.writeText('{{ $roomUrl }}'); toastr.success('Link meeting berhasil disalin!');">
                        <i class="fas fa-copy me-1"></i> Salin Link
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row h-100">
        <div class="col-12 h-100">
            <div class="card" style="height: 75vh;">
                <div class="card-body p-0">
                    <div id="meet" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://{{ \App\Models\Setting::getValue('jitsi_domain', 'meet.jit.si') }}/external_api.js"></script>
<script>
    const domain = '{{ \App\Models\Setting::getValue('jitsi_domain', 'meet.jit.si') }}';
    const options = {
        roomName: 'simang_{{ $meeting->jitsi_room_id }}',
        width: '100%',
        height: '100%',
        parentNode: document.querySelector('#meet'),
        userInfo: {
            displayName: '{{ auth()->user()->name }}'
        },
        configOverwrite: {
            startWithAudioMuted: true,
            startWithVideoMuted: true
        },
        interfaceConfigOverwrite: {
            SHOW_JITSI_WATERMARK: false,
            SHOW_WATERMARK_FOR_GUESTS: false,
            DEFAULT_BACKGROUND: '#ffffff'
        }
    };
    const api = new JitsiMeetExternalAPI(domain, options);
</script>
@endpush
