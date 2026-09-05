@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Halo, Selamat Datang!</h4>
            <p class="mb-0">Super Admin Panel</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Admin</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Dashboard</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Stat Cards -->
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-primary">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-primary p-3 rounded-circle">
                        <i class="la la-users" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Pengguna</p>
                        <h3 class="text-white">{{ $stats['total_users'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-warning">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-warning p-3 rounded-circle">
                        <i class="la la-user-graduate" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Total Mahasiswa</p>
                        <h3 class="text-white">{{ $stats['total_students'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-success">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-success p-3 rounded-circle">
                        <i class="la la-building" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Mitra Industri</p>
                        <h3 class="text-white">{{ $stats['total_industries'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-info">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-info p-3 rounded-circle">
                        <i class="la la-rocket" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_internships'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Applications Table -->
    <div class="col-lg-8 col-md-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Pendaftaran Magang Terbaru</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Lowongan</strong></th>
                                <th><strong>Perusahaan</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Tanggal</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentApplications as $app)
                                <tr>
                                    <td>{{ $app->student->user->name }}</td>
                                    <td>{{ $app->vacancy->title }}</td>
                                    <td>{{ $app->vacancy->industry->name }}</td>
                                    <td>
                                        <span class="badge {{ $app->status_badge['class'] }}">
                                            {{ $app->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>{{ $app->created_at->format('d M Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada pengajuan magang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Status Chart -->
    <div class="col-lg-4 col-md-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Statistik Lamaran Magang</h4>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 280px;">
                <canvas id="adminAppChart" style="max-height: 220px; max-width: 220px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Registered Users -->
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Pengguna Terdaftar Baru</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Nama</strong></th>
                                <th><strong>Email</strong></th>
                                <th><strong>Peran / Role</strong></th>
                                <th><strong>Tanggal Registrasi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsers as $usr)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $usr->avatar_url }}" width="30" height="30" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <span class="text-dark" style="font-weight: 600;">{{ $usr->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $usr->email }}</td>
                                    <td>
                                        <span class="badge light badge-primary px-3 py-1">{{ $usr->getRoleLabel() }}</span>
                                    </td>
                                    <td>{{ $usr->created_at->translatedFormat('d M Y, H:i') }} WIB</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada pengguna baru terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('adminAppChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [ {!! implode(',', collect(array_keys($applicationStats))->map(fn($k) => "'$k'")->toArray()) !!} ],
                datasets: [{
                    data: [ {{ implode(',', array_values($applicationStats)) }} ],
                    backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
                    borderWidth: 2,
                    borderRadius: 4,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: 'Poppins',
                                size: 11,
                                weight: '500'
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
