@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Pengaturan Aplikasi</h4>
            <p class="mb-0">Ubah konfigurasi global sistem seperti nama aplikasi, kontak bantuan, dan ambang batas kelayakan magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Pengaturan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Konfigurasi Sistem</h4>
            </div>
            <div class="card-body">
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs mb-4" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#general" role="tab">
                            <i class="la la-cog me-1"></i> Umum
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#academic" role="tab">
                            <i class="la la-graduation-cap me-1"></i> Akademik
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#contact" role="tab">
                            <i class="la la-phone me-1"></i> Kontak
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#google-sso" role="tab">
                            <i class="fab fa-google me-1"></i> Google SSO
                        </a>
                    </li>
                </ul>

                <!-- Form Start -->
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Tab Contents -->
                    <div class="tab-content">
                        <!-- Tab 1: General Settings -->
                        <div class="tab-pane active" id="general" role="tabpanel">
                            <div class="p-3">
                                <h5 class="text-dark mb-4" style="font-weight: 700;">Pengaturan Umum</h5>
                                
                                @foreach($settings->get('general', collect()) as $setting)
                                    <div class="form-group mb-4">
                                        <label class="form-label text-dark font-weight-bold" for="set_{{ $setting->key }}">
                                            {{ $setting->label }}
                                        </label>
                                        
                                        @if(in_array($setting->key, ['app_logo', 'app_icon', 'app_letterhead']))
                                            @if($setting->value)
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="Preview" style="max-height: 80px; border-radius: 4px; border: 1px solid #ddd; padding: 4px;">
                                                </div>
                                            @endif
                                            <input type="file" name="settings_files[{{ $setting->key }}]" id="set_{{ $setting->key }}" class="form-control" accept="image/*">
                                        @else
                                            <input type="text" name="settings[{{ $setting->key }}]" id="set_{{ $setting->key }}" 
                                                   class="form-control" value="{{ old('settings.' . $setting->key, $setting->value) }}" required>
                                        @endif

                                        @if($setting->description)
                                            <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tab 2: Academic Settings -->
                        <div class="tab-pane" id="academic" role="tabpanel">
                            <div class="p-3">
                                <h5 class="text-dark mb-4" style="font-weight: 700;">Batas Kelayakan Akademik</h5>
                                
                                @foreach($settings->get('academic', collect()) as $setting)
                                    <div class="form-group mb-4">
                                        <label class="form-label text-dark font-weight-bold" for="set_{{ $setting->key }}">
                                            {{ $setting->label }}
                                        </label>
                                        @if(in_array($setting->key, ['use_campus_geofencing', 'use_industry_geofencing']))
                                            <select name="settings[{{ $setting->key }}]" id="set_{{ $setting->key }}" class="form-control" required>
                                                <option value="0" {{ old('settings.' . $setting->key, $setting->value) == '0' ? 'selected' : '' }}>0 - Dimatikan (Bebas Radius)</option>
                                                <option value="1" {{ old('settings.' . $setting->key, $setting->value) == '1' ? 'selected' : '' }}>1 - Diaktifkan (Cek Radius)</option>
                                            </select>
                                        @else
                                            <input type="{{ $setting->key === 'min_gpa' ? 'number' : 'number' }}" 
                                                   step="{{ $setting->key === 'min_gpa' ? '0.01' : '1' }}"
                                                   name="settings[{{ $setting->key }}]" id="set_{{ $setting->key }}" 
                                                   class="form-control" value="{{ old('settings.' . $setting->key, $setting->value) }}" required>
                                        @endif
                                        @if($setting->description)
                                            <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tab 3: Contact Settings -->
                        <div class="tab-pane" id="contact" role="tabpanel">
                            <div class="p-3">
                                <h5 class="text-dark mb-4" style="font-weight: 700;">Hubungan & Bantuan</h5>
                                
                                @foreach($settings->get('contact', collect()) as $setting)
                                    <div class="form-group mb-4">
                                        <label class="form-label text-dark font-weight-bold" for="set_{{ $setting->key }}">
                                            {{ $setting->label }}
                                        </label>
                                        <input type="{{ $setting->key === 'contact_email' ? 'email' : 'text' }}" 
                                               name="settings[{{ $setting->key }}]" id="set_{{ $setting->key }}" 
                                               class="form-control" value="{{ old('settings.' . $setting->key, $setting->value) }}" required>
                                        @if($setting->description)
                                            <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Tab 4: Google SSO Settings -->
                        <div class="tab-pane" id="google-sso" role="tabpanel">
                            <div class="p-3">
                                <h5 class="text-dark mb-4" style="font-weight: 700;">Pengaturan Google SSO (OAuth)</h5>
                                
                                @foreach($settings->get('google', collect()) as $setting)
                                    <div class="form-group mb-4">
                                        <label class="form-label text-dark font-weight-bold" for="set_{{ $setting->key }}">
                                            {{ $setting->label }}
                                        </label>
                                        <input type="text" name="settings[{{ $setting->key }}]" id="set_{{ $setting->key }}" 
                                               class="form-control" value="{{ old('settings.' . $setting->key, $setting->value) }}">
                                        @if($setting->description)
                                            <small class="text-muted d-block mt-1">{{ $setting->description }}</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Submit & Reset Buttons -->
                    <div class="d-flex gap-3 justify-content-end border-top pt-4 mt-5">
                        <button type="submit" class="btn btn-primary px-4 py-2" style="font-size: 13px; font-weight: 600;">
                            <i class="la la-save me-1"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
