<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Internship Management System</title>
    
    <!-- Favicon icon -->
    @php
        $appIcon = \App\Models\Setting::getValue('app_icon');
        $iconUrl = $appIcon ? asset('storage/' . $appIcon) : asset('edumin/images/favicon.png');
    @endphp
    <link rel="icon" type="image/png" sizes="16x16" href="{{ $iconUrl }}">
    
    <!-- PWA / Web App Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#6366F1">
    <link rel="apple-touch-icon" href="{{ $iconUrl }}">
    
    <!-- STYLESHEETS -->
    <link rel="stylesheet" href="{{ asset('edumin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('edumin/vendor/toastr/css/toastr.min.css') }}">
    <link class="main-css" rel="stylesheet" href="{{ asset('edumin/css/style.css') }}">
    
    <!-- FontAwesome & LineAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* Smooth Transition for Dark Mode & Theme elements */
        body, .content-body, .card, .dlabnav, .header, .footer, .nav-header {
            transition: background 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        background-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        border-color 0.4s cubic-bezier(0.25, 0.8, 0.25, 1), 
                        box-shadow 0.4s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
        }
        
        /* Premium Card Hover Effects */
        .widget-stat.card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .widget-stat.card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15) !important;
        }
        
        .card.shadow-sm {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
            border: 1px solid rgba(0, 0, 0, 0.03) !important;
        }
        [data-theme-version="dark"] .card.shadow-sm {
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }
        .card.shadow-sm:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
        }
        
        /* Modern Gradient Logo Text */
        .brand-logo-text {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 800 !important;
            font-size: 20px !important;
            letter-spacing: 1.5px !important;
            background: linear-gradient(135deg, #1f2937 30%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-left: 10px !important;
        }
        
        [data-theme-version="dark"] .brand-logo-text {
            background: linear-gradient(135deg, #ffffff 30%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .brand-logo-icon {
            width: 45px;
            height: 45px;
            transition: all 0.3s ease;
        }
        .brand-logo:hover .brand-logo-icon {
            transform: scale(1.05) rotate(-5deg);
        }

        /* Dynamic Theme Switcher Panel */
        .theme-settings-btn {
            position: fixed;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            color: #fff;
            padding: 12px 15px;
            cursor: pointer;
            z-index: 9999;
            border-radius: 20px 0 0 20px;
            box-shadow: -4px 4px 10px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .theme-settings-btn:hover {
            padding-right: 20px;
        }
        .theme-settings-panel {
            position: fixed;
            right: -320px;
            top: 0;
            width: 320px;
            height: 100vh;
            background: #fff;
            z-index: 10000;
            box-shadow: -5px 0 20px rgba(0,0,0,0.1);
            transition: right 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
        }
        [data-theme-version="dark"] .theme-settings-panel {
            background: #1e293b;
            color: #f8fafc;
        }
        .theme-settings-panel.active {
            right: 0;
        }
        .color-swatch {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: transform 0.2s;
        }
        .color-swatch:hover {
            transform: scale(1.1);
        }
        .color-swatch.active {
            border-color: #333;
            box-shadow: 0 0 0 2px #fff inset;
        }
        [data-theme-version="dark"] .color-swatch.active {
            border-color: #f8fafc;
            box-shadow: 0 0 0 2px #1e293b inset;
        }

        /* Normal Mobile Layout Safety Polish */
        html, body {
            overflow-x: hidden !important;
            max-width: 100% !important;
        }

        /* Mobile & Tablet Responsive Layout Override */
        @media (max-width: 991.98px) {
            .content-body {
                margin-left: 0 !important;
                width: 100% !important;
                padding-left: 15px !important;
                padding-right: 15px !important;
            }
            .footer {
                padding-left: 0 !important;
            }
            .dlabnav {
                position: fixed !important;
                top: 70px !important;
                left: -290px !important;
                width: 260px !important;
                height: calc(100vh - 70px) !important;
                z-index: 99999 !important;
                transition: left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1) !important;
                box-shadow: 10px 0 30px rgba(0, 0, 0, 0.15) !important;
                background: #ffffff !important;
            }
            [data-theme-version="dark"] .dlabnav {
                background: #1e1e2d !important;
            }
            #main-wrapper.menu-toggle .dlabnav {
                left: 0 !important;
            }
            #main-wrapper.menu-toggle .content-body {
                margin-left: 0 !important;
            }
            #main-wrapper.menu-toggle::after {
                content: "";
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 99990;
            }
            .header {
                width: 100% !important;
                padding-left: 0 !important;
            }
            .table-responsive {
                display: block !important;
                width: 100% !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch;
            }
            .page-titles {
                display: flex;
                flex-direction: column;
                align-items: flex-start !important;
                padding: 15px !important;
            }
            .page-titles .breadcrumb {
                margin-top: 8px !important;
            }
            .welcome-text h4 {
                font-size: 18px !important;
                line-height: 1.4 !important;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Preloader start -->
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <!-- Preloader end -->
    <!-- Fast Preloader Dismiss for Mobile & Desktop -->
    <script>
        (function() {
            function hidePreloader() {
                var preloader = document.getElementById('preloader');
                var mainWrapper = document.getElementById('main-wrapper');
                if (preloader && preloader.style.display !== 'none') {
                    preloader.style.transition = 'opacity 0.3s ease';
                    preloader.style.opacity = '0';
                    setTimeout(function() { preloader.style.display = 'none'; }, 300);
                }
                if (mainWrapper) {
                    mainWrapper.classList.add('show');
                }
            }
            if (document.readyState === 'complete' || document.readyState === 'interactive') {
                setTimeout(hidePreloader, 100);
            } else {
                document.addEventListener('DOMContentLoaded', hidePreloader);
                window.addEventListener('load', hidePreloader);
            }
            // Absolute failsafe: never let preloader stay longer than 800ms
            setTimeout(hidePreloader, 800);
        })();
    </script>

    <!-- Main wrapper start -->
    <div id="main-wrapper">

        <!-- Nav header start -->
        <div class="nav-header">
            <a href="{{ route('dashboard.redirect') }}" class="brand-logo" style="display: flex; align-items: center; text-decoration: none;">
                @php
                    $appLogo = \App\Models\Setting::getValue('app_logo');
                    $logoUrl = $appLogo ? asset('storage/' . $appLogo) : asset('edumin/images/logo-white.png');
                    $blendMode = $appLogo ? 'mix-blend-mode: multiply;' : '';
                @endphp
                <img class="logo-abbr" src="{{ $logoUrl }}" alt="" style="max-width: 50px; max-height: 50px; {{ $blendMode }}">
                @if(!$appLogo)
                <span class="brand-title text-white font-weight-bold" style="margin-left: 10px; font-size: 1.2rem;">{{ \App\Models\Setting::getValue('app_short_name', 'SIMANG') }}</span>
                @else
                <span class="brand-title text-white font-weight-bold" style="margin-left: 10px; font-size: 1.2rem;">{{ \App\Models\Setting::getValue('app_short_name', 'SIMANG') }}</span>
                @endif
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!-- Nav header end -->

        <!-- Header start -->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
                            <div class="search_bar dropdown">
                                <span class="search_icon p-3 c-pointer" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-magnify"></i>
                                </span>
                                <div class="dropdown-menu p-0 m-0">
                                    <form>
                                        <input class="form-control" type="search" placeholder="Cari..." aria-label="Search">
                                    </form>
                                </div>
                            </div>
                        </div>

                        <ul class="navbar-nav header-right">
                            <!-- Notification Bell -->
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell ai-icon" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-bell"></i>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <div class="pulse-css"></div>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <div id="DZ_W_Notification1" class="widget-media dlab-scroll p-3" style="height:380px;">
                                        <ul class="timeline">
                                            @forelse(auth()->user()->unreadNotifications as $notification)
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media-body">
                                                            <h6 class="mb-1">{{ $notification->data['title'] ?? 'Notifikasi' }}</h6>
                                                            <small class="d-block">{{ $notification->data['message'] ?? '' }}</small>
                                                            <a href="{{ url('notifications/'.$notification->id.'/read') }}" class="btn btn-primary btn-xxs shadow mt-2">Lihat Detail</a>
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="text-center text-muted">Belum ada notifikasi baru.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    @if(auth()->user()->unreadNotifications->count() > 0)
                                        <a class="all-notification" href="{{ url('notifications/mark-all-read') }}">Tandai Semua Dibaca <i class="ti-arrow-right"></i></a>
                                    @endif
                                </div>
                            </li>
                            <!-- Theme Mode Toggle -->
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dlab-theme-mode p-0" href="javascript:void(0);">
                                    <i id="icon-light" class="fas fa-sun"></i>
                                    <i id="icon-dark" class="fas fa-moon"></i>
                                </a>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
                                    <img src="{{ auth()->user()->avatar_url }}" width="20" class="rounded-circle" alt=""/>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="{{ route('profile') }}" class="dropdown-item ai-icon">
                                        <i class="la la-user text-primary"></i>
                                        <span class="ms-2">Profil Saya</span>
                                    </a>
                                    <a href="javascript:void(0);" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item ai-icon">
                                        <i class="la la-sign-out-alt text-danger"></i>
                                        <span class="ms-2">Keluar</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Header end -->

        <!-- Sidebar start -->
        <div class="dlabnav">
            <div class="dlabnav-scroll">
                <ul class="metismenu" id="menu">
                    <li class="nav-label first">Main Menu</li>
                    
                    @if(auth()->user()->hasRole('super-admin'))
                        <!-- SUPER ADMIN MENUS -->
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-user-circle"></i>
                                <span class="nav-text">Manajemen User</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.faculties.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-university"></i>
                                <span class="nav-text">Fakultas</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.study-programs.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-graduation-cap"></i>
                                <span class="nav-text">Program Studi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.periods.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-calendar-check"></i>
                                <span class="nav-text">Periode Magang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.industries.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-building"></i>
                                <span class="nav-text">Mitra Industri</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-cog"></i>
                                <span class="nav-text">Pengaturan</span>
                            </a>
                        </li>
                    
                    @elseif(auth()->user()->hasRole('finance'))
                        <!-- FINANCE MENUS -->
                        <li>
                            <a href="{{ route('finance.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('finance.payments.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-money-bill"></i>
                                <span class="nav-text">Validasi SPP</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('baak'))
                        <!-- BAAK MENUS -->
                        <li>
                            <a href="{{ route('baak.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('baak.sks.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-file-alt"></i>
                                <span class="nav-text">Input SKS Mahasiswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('baak.grade-conversions.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-exchange-alt"></i>
                                <span class="nav-text">Konversi Nilai Akhir</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('kaprodi'))
                        <!-- KAPRODI MENUS -->
                        <li>
                            <a href="{{ route('kaprodi.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kaprodi.applications.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-file-signature"></i>
                                <span class="nav-text">Validasi Akademik</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kaprodi.dpl-plotting.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-users-cog"></i>
                                <span class="nav-text">Plotting DPL</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kaprodi.internships.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-laptop-code"></i>
                                <span class="nav-text">Program Magang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('kaprodi.statistics') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-chart-bar"></i>
                                <span class="nav-text">Statistik Prodi</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('dekan'))
                        <!-- DEKAN MENUS -->
                        <li>
                            <a href="{{ route('dekan.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dekan.internships') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-file-alt"></i>
                                <span class="nav-text">Laporan Magang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dekan.industries') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-briefcase"></i>
                                <span class="nav-text">Kerjasama Industri</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('dpl'))
                        <!-- DPL MENUS -->
                        <li>
                            <a href="{{ route('dpl.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dpl.students') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-user-friends"></i>
                                <span class="nav-text">Daftar Bimbingan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dpl.logbooks.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-book-open"></i>
                                <span class="nav-text">Logbook Mahasiswa</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dpl.meetings.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-video"></i>
                                <span class="nav-text">Online Meeting</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dpl.attendance.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-calendar-check"></i>
                                <span class="nav-text">Monitoring Presensi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dpl.assessment.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-star"></i>
                                <span class="nav-text">Penilaian Akhir</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('supervisor-industri'))
                        <!-- SUPERVISOR INDUSTRI MENUS -->
                        <li>
                            <a href="{{ route('industry.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('industry.vacancies.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-clipboard-list"></i>
                                <span class="nav-text">Kelola Lowongan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('industry.agreements.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-file-contract"></i>
                                <span class="nav-text">Internship Agreement</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('industry.logbooks.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-book"></i>
                                <span class="nav-text">Logbook Harian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('supervisor.meetings.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-video"></i>
                                <span class="nav-text">Online Meeting</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('industry.attendance.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-map-marker"></i>
                                <span class="nav-text">Monitoring Kehadiran</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('industry.assessment.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-star-half-alt"></i>
                                <span class="nav-text">Input Nilai Industri</span>
                            </a>
                        </li>

                    @elseif(auth()->user()->hasRole('mahasiswa'))
                        <!-- MAHASISWA MENUS -->
                        <li>
                            <a href="{{ route('student.dashboard') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-home"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.vacancies.browse') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-search-location"></i>
                                <span class="nav-text">Cari Lowongan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.applications.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-history"></i>
                                <span class="nav-text">Lamaran Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.attendance.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-user-check"></i>
                                <span class="nav-text">Presensi Harian</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.logbooks.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-journal-whills"></i>
                                <span class="nav-text">Logbook Jurnal</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.meetings.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-video"></i>
                                <span class="nav-text">Online Meeting</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.report.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-file-upload"></i>
                                <span class="nav-text">Laporan Akhir</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('student.certificate.index') }}" class="ai-icon" aria-expanded="false">
                                <i class="la la-certificate"></i>
                                <span class="nav-text">Sertifikat</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
        <!-- Sidebar end -->

        <!-- Content body start -->
        <div class="content-body">
            <!-- row -->
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Berhasil!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
        <!-- Content body end -->

        <!-- Footer start -->
        <div class="footer">
            <div class="copyright">
                <p>Hak Cipta © {{ now()->year }} Horizon University Indonesia. Template by <a href="http://dexignlab.com/" target="_blank">DexignLab</a></p>
            </div>
        </div>
        <!-- Footer end -->
        
        <!-- Theme Settings Button -->
        <div class="theme-settings-btn" id="themeSettingsBtn">
            <i class="fas fa-cog fa-spin fa-lg"></i>
        </div>

        <!-- Theme Settings Panel -->
        <div class="theme-settings-panel" id="themeSettingsPanel">
            <div class="panel-header" style="padding: 20px; border-bottom: 1px solid rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center;">
                <h4 class="mb-0" style="font-weight: 700;">Pengaturan Tema</h4>
                <i class="fas fa-times text-danger" id="closeSettingsBtn" style="cursor: pointer; font-size: 20px;"></i>
            </div>
            <div class="panel-body" style="padding: 24px;">
                <div class="mb-4">
                    <h6 class="mb-3" style="font-weight: 600;">Mode Tampilan</h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm flex-fill theme-mode-btn" data-theme="light"><i class="fas fa-sun me-1"></i> Terang</button>
                        <button type="button" class="btn btn-outline-primary btn-sm flex-fill theme-mode-btn" data-theme="dark"><i class="fas fa-moon me-1"></i> Gelap</button>
                    </div>
                </div>
                
                <hr style="opacity: 0.1; margin: 20px 0;">
                
                <div class="mb-4">
                    <h6 class="mb-3" style="font-weight: 600;">Warna Utama</h6>
                    <div class="color-options d-flex flex-wrap gap-2" id="primaryColorOptions">
                        <!-- EduMin Default Colors -->
                        <div class="color-swatch bg-primary" data-prop="primary" data-color="color_1" style="background-color: #593bdb !important;"></div>
                        <div class="color-swatch" data-prop="primary" data-color="color_2" style="background-color: #ffaa16 !important;"></div>
                        <div class="color-swatch" data-prop="primary" data-color="color_3" style="background-color: #f25521 !important;"></div>
                        <div class="color-swatch" data-prop="primary" data-color="color_4" style="background-color: #11a052 !important;"></div>
                        <div class="color-swatch" data-prop="primary" data-color="color_5" style="background-color: #ff337a !important;"></div>
                        <div class="color-swatch" data-prop="primary" data-color="color_6" style="background-color: #00d2ff !important;"></div>
                    </div>
                </div>

                <hr style="opacity: 0.1; margin: 20px 0;">

                <div class="mb-4">
                    <h6 class="mb-3" style="font-weight: 600;">Warna Sidebar</h6>
                    <div class="color-options d-flex flex-wrap gap-2" id="sidebarColorOptions">
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_1" style="background-color: #fff !important; border: 2px solid #ddd;"></div>
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_2" style="background-color: #000 !important;"></div>
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_3" style="background-color: #593bdb !important;"></div>
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_4" style="background-color: #ffaa16 !important;"></div>
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_5" style="background-color: #f25521 !important;"></div>
                        <div class="color-swatch" data-prop="sidebarBg" data-color="color_8" style="background-color: #2196f3 !important;"></div>
                    </div>
                </div>

                <hr style="opacity: 0.1; margin: 20px 0;">

                <div class="mb-4">
                    <h6 class="mb-3" style="font-weight: 600;">Gaya Sidebar</h6>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm sidebar-style-btn" data-style="full">Penuh (Full)</button>
                        <button type="button" class="btn btn-outline-primary btn-sm sidebar-style-btn" data-style="mini">Kecil (Mini)</button>
                        <button type="button" class="btn btn-outline-primary btn-sm sidebar-style-btn" data-style="compact">Kompak</button>
                        <button type="button" class="btn btn-outline-primary btn-sm sidebar-style-btn" data-style="modern">Modern</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Main wrapper end -->

    <!-- Scripts -->
    <script src="{{ asset('edumin/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('edumin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('edumin/vendor/toastr/js/toastr.min.js') }}"></script>
    
    <!-- Custom scripts -->
    <script src="{{ asset('edumin/js/custom.min.js') }}"></script>
    <script src="{{ asset('edumin/js/dlabnav-init.js') }}"></script>
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Theme Settings Script -->
    <script>
        $(document).ready(function() {
            // Theme settings panel toggle
            $('#themeSettingsBtn').on('click', function() {
                $('#themeSettingsPanel').addClass('active');
            });
            $('#closeSettingsBtn').on('click', function() {
                $('#themeSettingsPanel').removeClass('active');
            });
            
            // Close panel when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#themeSettingsPanel, #themeSettingsBtn').length) {
                    $('#themeSettingsPanel').removeClass('active');
                }
            });

            // Initialize active states based on current settings
            var currentVersion = getCookie('version') || 'light';
            var currentPrimary = getCookie('primary') || 'color_1';
            var currentSidebar = getCookie('sidebarBg') || 'color_1';
            var currentSidebarStyle = getCookie('sidebarStyle') || 'full';
            
            $('.theme-mode-btn[data-theme="' + currentVersion + '"]').addClass('active btn-primary').removeClass('btn-outline-primary');
            $('#primaryColorOptions .color-swatch[data-color="' + currentPrimary + '"]').addClass('active');
            $('#sidebarColorOptions .color-swatch[data-color="' + currentSidebar + '"]').addClass('active');
            $('.sidebar-style-btn[data-style="' + currentSidebarStyle + '"]').addClass('active btn-primary').removeClass('btn-outline-primary');

            // Handle Theme Mode
            $('.theme-mode-btn').on('click', function() {
                var theme = $(this).data('theme');
                
                // Update UI buttons
                $('.theme-mode-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                
                // Set cookie and trigger dlabSettings via dlabnav-init logic if possible
                setCookie('version', theme);
                $('body').attr('data-theme-version', theme);
                
                // Update dlabnav active button state
                if(theme === 'dark') {
                    $('.dlab-theme-mode').addClass('active');
                } else {
                    $('.dlab-theme-mode').removeClass('active');
                }
            });

            // Handle Colors
            $('.color-swatch').on('click', function() {
                var color = $(this).data('color');
                var prop = $(this).data('prop');
                
                // Update active state in UI
                $(this).parent().find('.color-swatch').removeClass('active');
                $(this).addClass('active');
                
                // Apply setting directly to body
                if(prop === 'primary') {
                    $('body').attr('data-primary', color);
                    setCookie('primary', color);
                } else if(prop === 'sidebarBg') {
                    $('body').attr('data-sidebarbg', color);
                    setCookie('sidebarBg', color);
                }
            });

            // Handle Sidebar Style
            $('.sidebar-style-btn').on('click', function() {
                var style = $(this).data('style');
                
                // Update UI buttons
                $('.sidebar-style-btn').removeClass('active btn-primary').addClass('btn-outline-primary');
                $(this).addClass('active btn-primary').removeClass('btn-outline-primary');
                
                // Apply setting directly to body
                $('body').attr('data-sidebar-style', style);
                setCookie('sidebarStyle', style);
                
                // Trigger resize event to fix any layout glitches (like chart resizing)
                setTimeout(function(){ $(window).trigger('resize'); }, 300);
            });
            
            // Apply saved color settings on load
            if(currentPrimary) { $('body').attr('data-primary', currentPrimary); }
            if(currentSidebar) { $('body').attr('data-sidebarbg', currentSidebar); }
            if(currentSidebarStyle) { $('body').attr('data-sidebar-style', currentSidebarStyle); }
        });
    </script>
    
    @yield('scripts')
    @stack('scripts')
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register("{{ asset('sw.js') }}").then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <script>
        // Mobile Sidebar Auto-Close on Backdrop Click
        document.addEventListener('DOMContentLoaded', function() {
            var mainWrapper = document.getElementById('main-wrapper');
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 991 && mainWrapper && mainWrapper.classList.contains('menu-toggle')) {
                    var sidebar = document.querySelector('.dlabnav');
                    var hamburger = document.querySelector('.nav-control');
                    if (sidebar && !sidebar.contains(e.target) && hamburger && !hamburger.contains(e.target)) {
                        mainWrapper.classList.remove('menu-toggle');
                        var hamburgerBtn = document.querySelector('.hamburger');
                        if (hamburgerBtn) hamburgerBtn.classList.remove('is-active');
                    }
                }
            });
        });
    </script>
    <script>
        // Web Audio Synthesizer Chime for Notifications
        function playNotificationChime() {
            try {
                var AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                var ctx = new AudioContext();
                var now = ctx.currentTime;
                
                // First note: 587.33 Hz (D5)
                var osc1 = ctx.createOscillator();
                var gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(587.33, now);
                gain1.gain.setValueAtTime(0.3, now);
                gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(now);
                osc1.stop(now + 0.35);
                
                // Second note: 880.00 Hz (A5)
                var osc2 = ctx.createOscillator();
                var gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(880.00, now + 0.15);
                gain2.gain.setValueAtTime(0.45, now + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.65);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(now + 0.15);
                osc2.stop(now + 0.65);
            } catch (e) {
                console.warn('Audio play prevented or unsupported:', e);
            }
        }

        // Real-time background polling every 30 seconds
        (function() {
            var lastUnreadCount = -1;

            function checkNotifications() {
                fetch("{{ route('notifications.check') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (lastUnreadCount !== -1 && data.unread_count > lastUnreadCount) {
                        playNotificationChime();
                        if (data.latest && data.latest.length > 0) {
                            var latestNotif = data.latest[0];
                            if (typeof toastr !== 'undefined') {
                                toastr.info(latestNotif.message, latestNotif.title, {
                                    timeOut: 6000,
                                    closeButton: true,
                                    progressBar: true
                                });
                            }
                        }
                    }
                    lastUnreadCount = data.unread_count;

                    // Update pulse indicator on bell icon
                    var bellLink = document.querySelector('.notification_dropdown .bell');
                    if (bellLink) {
                        var pulse = bellLink.querySelector('.pulse-css');
                        if (data.unread_count > 0 && !pulse) {
                            var p = document.createElement('div');
                            p.className = 'pulse-css';
                            bellLink.appendChild(p);
                        } else if (data.unread_count === 0 && pulse) {
                            pulse.remove();
                        }
                    }

                    // Update notification dropdown timeline list
                    var timeline = document.querySelector('#DZ_W_Notification1 .timeline');
                    if (timeline && data.latest) {
                        if (data.latest.length === 0) {
                            timeline.innerHTML = '<li class="text-center text-muted">Belum ada notifikasi baru.</li>';
                        } else {
                            var html = '';
                            data.latest.forEach(function(item) {
                                html += '<li>' +
                                    '<div class="timeline-panel">' +
                                        '<div class="media-body">' +
                                            '<h6 class="mb-1">' + item.title + '</h6>' +
                                            '<small class="d-block">' + item.message + '</small>' +
                                            '<a href="' + item.url + '" class="btn btn-primary btn-xxs shadow mt-2">Lihat Detail</a>' +
                                        '</div>' +
                                    '</div>' +
                                '</li>';
                            });
                            timeline.innerHTML = html;
                        }
                    }
                })
                .catch(err => console.debug('Notif poll error:', err));
            }

            setInterval(checkNotifications, 30000);
            setTimeout(checkNotifications, 2000);
        })();
    </script>
</body>
</html>
