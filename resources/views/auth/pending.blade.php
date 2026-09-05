@extends('layouts.auth')

@section('content')
<div class="fix-wrapper w-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card mb-0 h-auto shadow-lg" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="la la-user-clock text-warning" style="font-size: 80px;"></i>
                        </div>
                        <h3 class="mb-3" style="font-weight: 700; color: #0F172A;">Akun Menunggu Persetujuan</h3>
                        <p class="text-muted leading-relaxed">
                            Halo <strong>{{ auth()->user()->name }}</strong>, akun Anda telah berhasil didaftarkan via Google Sign-In. 
                            Namun, administrator atau Kaprodi belum menetapkan peran (role) untuk akun Anda.
                        </p>
                        <div class="alert alert-info border-0 py-3 mt-4" style="background-color: #F0F9FF; color: #0369A1;">
                            <i class="la la-info-circle me-2"></i> Silakan hubungi admin akademik (BAAK) atau Kaprodi untuk mengaktifkan akses masuk Anda.
                        </div>
                        <div class="mt-5 d-flex gap-3 justify-content-center">
                            <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger btn-lg px-4">
                                <i class="la la-sign-out-alt me-1"></i> Keluar
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
