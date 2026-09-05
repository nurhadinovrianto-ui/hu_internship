@extends('layouts.auth')

@section('title', 'Masuk')

@section('styles')
<style>
    .btn-google-login {
        border: 1px solid #dadce0 !important;
        border-radius: 8px !important;
        font-weight: 600 !important;
        color: #3c4043 !important;
        background-color: #ffffff !important;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
    }
    .btn-google-login:hover {
        background-color: #f8f9fa !important;
        border-color: #d2e3fc !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
        transform: translateY(-1px);
    }
    .auth-card {
        border-radius: 16px !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08) !important;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
    }
    .form-control {
        border-radius: 8px !important;
        padding: 10px 15px !important;
        height: auto !important;
        border: 1px solid #cbd5e1 !important;
    }
    .form-control:focus {
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }
    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #888;
        margin: 20px 0;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }
    .divider:not(:empty)::before {
        margin-right: .25em;
    }
    .divider:not(:empty)::after {
        margin-left: .25em;
    }
</style>
@endsection

@section('content')
<div class="fix-wrapper w-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-9">
                <div class="card auth-card mb-0 h-auto">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <a href="/" style="display: inline-flex; align-items: center; justify-content: center; text-decoration: none; flex-direction: column;">
                                @php
                                    $appLogo = \App\Models\Setting::getValue('app_logo');
                                    $logoUrl = $appLogo ? asset('storage/' . $appLogo) : asset('edumin/images/logo-white.png');
                                @endphp
                                <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 80px; max-width: 80px; margin-bottom: 10px;">
                                <h2 class="text-primary font-weight-bold" style="letter-spacing: 0.5px; font-size: 24px;">{{ \App\Models\Setting::getValue('app_name', 'Internship Management System') }}</h2>
                            </a>
                            <p class="text-muted mt-2" style="font-size: 14px;">Masuk ke Akun Anda</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show p-3" style="border-radius: 8px;">
                                <div class="d-flex align-items-center">
                                    <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                                    <span style="font-size: 13px;">{{ $errors->first() }}</span>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close" style="padding: 1rem;"></button>
                            </div>
                        @endif

                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="mb-1 text-dark font-weight-bold" for="email" style="font-size: 13px;">Alamat Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                            </div>
                            <div class="form-group mb-3 position-relative">
                                <label class="mb-1 text-dark font-weight-bold" for="dlabPassword" style="font-size: 13px;">Kata Sandi</label>
                                <input type="password" name="password" id="dlabPassword" class="form-control" placeholder="••••••••" required>
                                <span class="show-pass eye" style="top: 38px;">
                                    <i class="fa fa-eye-slash"></i>
                                    <i class="fa fa-eye"></i>
                                </span>
                            </div>
                            <div class="form-row d-flex justify-content-between mt-4 mb-4">
                                <div class="form-group">
                                   <div class="form-check custom-checkbox">
                                        <input type="checkbox" name="remember" class="form-check-input" id="basic_checkbox_1">
                                        <label class="form-check-label text-muted" for="basic_checkbox_1" style="font-size: 13px; font-weight: 500;">Ingat saya di perangkat ini</label>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-block btn-lg" style="border-radius: 8px; font-weight: 600; padding: 12px 20px;">Masuk</button>
                            </div>
                        </form>

                        <div class="divider" style="font-size: 12px; font-weight: 500;">atau</div>

                        <div class="text-center">
                            <a href="{{ route('auth.google') }}" class="btn btn-google-login btn-block btn-lg d-flex justify-content-center align-items-center py-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48" class="me-2">
                                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                    <path fill="#4285F4" d="M46.5 24c0-1.55-.15-3.24-.47-4.77H24v9.03h12.75c-.55 2.97-2.22 5.49-4.75 7.18l7.39 5.73C43.7 36.88 46.5 31.05 46.5 24z"/>
                                    <path fill="#FBBC05" d="M10.54 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.98-6.19z"/>
                                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.39-5.73c-2.11 1.41-4.81 2.3-8.5 2.3-6.26 0-11.57-4.22-13.46-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                </svg>
                                Masuk dengan Google
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
