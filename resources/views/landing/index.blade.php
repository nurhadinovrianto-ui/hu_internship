<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KMS-FICT — {{ \App\Models\Setting::getValue('app_name', 'Sistem Magang') }}</title>
    
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('edumin/images/favicon.png') }}">
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="{{ asset('edumin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link class="main-css" rel="stylesheet" href="{{ asset('edumin/css/style.css') }}">
    
    <!-- Fonts Poppins & Nunito (Matching Edumin default fonts) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #f3f6f9 !important;
            color: #1e1e2d;
            overflow-x: hidden;
        }
        
        .navbar-landing {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 15px 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            transition: all 0.3s ease;
        }
        
        .brand-logo-text {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 800 !important;
            font-size: 22px !important;
            letter-spacing: 2px !important;
            background: linear-gradient(135deg, #1e1e2d 30%, #6366f1 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-left: 8px !important;
        }
        
        .brand-logo-abbr {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 900 !important;
            font-size: 20px !important;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            border: 2px solid #6366f1;
            padding: 1px 8px;
            border-radius: 8px;
        }
        
        .hero-section {
            position: relative;
            padding: 140px 0 80px 0;
            background-image: linear-gradient(135deg, #eef2f7 0%, #f3f6f9 100%);
        }
        
        .hero-title {
            font-size: 48px;
            font-weight: 800;
            line-height: 1.2;
            color: #1e1e2d;
            margin-bottom: 24px;
        }
        
        .hero-title span {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-desc {
            font-size: 16px;
            color: #5e6278;
            line-height: 1.6;
            margin-bottom: 35px;
            max-width: 620px;
        }
        
        /* Card & Animation Styles standard to our optimized layout */
        .premium-card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
            border-radius: 12px !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03) !important;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
        }
        
        .feature-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        .icon-indigo {
            background-color: rgba(99, 102, 241, 0.1);
            color: #6366F1;
        }
        
        .icon-purple {
            background-color: rgba(168, 85, 247, 0.1);
            color: #A855F7;
        }
        
        .icon-emerald {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        
        .btn-premium {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white !important;
            border: none;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-premium:hover {
            opacity: 0.95;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }
        
        .timeline-number {
            font-size: 14px;
            font-weight: 700;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-landing fixed-top">
        <div class="container">
            <a class="brand-logo" href="#" style="display: flex; align-items: center; text-decoration: none;">
                @php
                    $appLogo = \App\Models\Setting::getValue('app_logo');
                    $logoUrl = $appLogo ? asset('storage/' . $appLogo) : asset('edumin/images/logo-white.png');
                    $blendMode = $appLogo ? 'mix-blend-mode: multiply;' : '';
                @endphp
                <img class="logo-abbr" src="{{ $logoUrl }}" alt="" style="max-width: 50px; max-height: 50px; {{ $blendMode }}">
                <span class="brand-logo-text">{{ \App\Models\Setting::getValue('app_short_name', 'SIMANG') }}</span>
            </a>
            <div class="ms-auto">
                <a href="{{ route('login') }}" class="btn btn-premium px-4 py-2" style="font-size: 13px;">Masuk Sistem</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center" style="min-height: 100vh;">
        <div class="container mt-4">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-12">
                    <h1 class="hero-title">
                        Platform Integrasi Magang <span>Kampus &amp; Industri</span>
                    </h1>
                    <p class="hero-desc">
                        Sistem manajemen magang (Internship Management System V3) mempercepat birokrasi, mengotomasi validasi prasyarat akademik-keuangan, serta menghubungkan mahasiswa dengan mentor industri secara seamless.
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('login') }}" class="btn btn-premium btn-lg px-4 py-3" style="font-size: 15px;">
                            <i class="la la-rocket me-2" style="font-size: 18px;"></i> Mulai Ajukan Magang
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 col-md-12 mt-5 mt-lg-0">
                    <div class="card premium-card">
                        <div class="card-body p-4 p-md-5">
                            <h4 class="mb-4 text-dark" style="font-weight: 700; letter-spacing: -0.5px;">Alur Sistem V3</h4>
                            <div class="timeline-widget">
                                <div class="d-flex mb-4">
                                    <div class="me-3">
                                        <span class="timeline-number bg-primary text-white">1</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-dark" style="font-weight: 600; font-size: 15px;">Gatekeeper Check</h6>
                                        <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.4;">Otomasi validasi pelunasan keuangan dan pemenuhan jumlah SKS minimum.</p>
                                    </div>
                                </div>
                                <div class="d-flex mb-4">
                                    <div class="me-3">
                                        <span class="timeline-number bg-info text-white">2</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-dark" style="font-weight: 600; font-size: 15px;">Kaprodi &amp; Industry Selection</h6>
                                        <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.4;">Persetujuan administratif program studi langsung diteruskan ke mitra industri.</p>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <span class="timeline-number bg-success text-white">3</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 text-dark" style="font-weight: 600; font-size: 15px;">Dual Assessment &amp; Cert</h6>
                                        <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.4;">Pengisian logbook harian, review mentor, konversi nilai SKS, dan auto-generate sertifikat.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section class="py-5" style="background-color: #ffffff; border-top: 1px solid rgba(0, 0, 0, 0.05); border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 style="font-weight: 800; color: #1e1e2d; letter-spacing: -0.5px;">Aktor &amp; Peran Terintegrasi</h2>
                <p class="text-muted" style="font-size: 14px;">Satu platform, delapan pengguna dengan tanggung jawab yang spesifik.</p>
            </div>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card premium-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon icon-indigo">
                                <i class="la la-user-graduate"></i>
                            </div>
                            <h5 class="text-dark mb-2" style="font-weight: 700;">Mahasiswa</h5>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">Mendaftar program magang, mencatat absensi koordinat GPS, logbook harian, dan unduh sertifikat magang.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card premium-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon icon-purple">
                                <i class="la la-chalkboard-teacher"></i>
                            </div>
                            <h5 class="text-dark mb-2" style="font-weight: 700;">Dosen Pembimbing</h5>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">Membimbing mahasiswa secara intensif, meninjau logbook harian, dan melakukan penilaian akademis laporan akhir.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card premium-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon icon-emerald">
                                <i class="la la-building"></i>
                            </div>
                            <h5 class="text-dark mb-2" style="font-weight: 700;">Supervisor Industri</h5>
                            <p class="text-muted mb-0" style="font-size: 13px; line-height: 1.5;">Membuat info lowongan magang, menyeleksi kandidat, memberikan bimbingan harian, dan menilai performa praktikal.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 text-center" style="background-color: #f3f6f9;">
        <p class="text-muted mb-0" style="font-size: 13px; font-weight: 500;">
            &copy; {{ now()->year }} Horizon University Indonesia. Seluruh hak cipta dilindungi.
        </p>
    </footer>

    <!-- Scripts -->
    <script src="{{ asset('edumin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('edumin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('edumin/js/custom.min.js') }}"></script>
</body>
</html>
