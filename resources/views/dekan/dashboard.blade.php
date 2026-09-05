@extends('layouts.app')

@section('title', 'Dekan Dashboard')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Dashboard Pemantauan Dekan @if(isset($faculty) && $faculty)<span class="badge bg-primary text-white ms-2" style="font-size: 14px;">{{ $faculty->name }}</span>@endif</h4>
            <p class="mb-0">Statistik real-time, sebaran mahasiswa magang, dan monitoring kemitraan industri khusus Fakultas Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dekan</a></li>
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
                        <i class="la la-graduation-cap" style="font-size: 24px;"></i>
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
                        <i class="la la-user-check" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Magang Aktif</p>
                        <h3 class="text-white">{{ $stats['active_internships'] }}</h3>
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
                        <i class="la la-handshake" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Mitra Aktif</p>
                        <h3 class="text-white">{{ $stats['partner_industries'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card bg-dark">
            <div class="card-body p-4">
                <div class="media align-items-center">
                    <span class="me-3 bg-white text-dark p-3 rounded-circle">
                        <i class="la la-award" style="font-size: 24px;"></i>
                    </span>
                    <div class="media-body text-white text-end">
                        <p class="mb-1">Lulus Magang</p>
                        <h3 class="text-white">{{ $stats['completed_internships'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Interns by study program -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Sebaran Magang Aktif per Program Studi</h4>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 280px;">
                @if($internshipsByProdi->isNotEmpty())
                    <canvas id="prodiChart" style="max-height: 250px; max-width: 250px;"></canvas>
                @else
                    <p class="text-muted mb-0">Belum ada sebaran data magang aktif.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Industry Partners -->
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Mitra Industri Terbanyak Menyerap Magang</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @forelse($topIndustries as $ind)
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <div>
                                <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $ind->name }}</h6>
                                <small class="text-muted">{{ $ind->industry_type }}</small>
                            </div>
                            <span class="badge badge-success text-white">{{ $ind->internship_count }} Diterima</span>
                        </li>
                    @empty
                        <li class="list-group-item text-center text-muted px-0">Belum ada penyerapan dari mitra industri.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($internshipsByProdi->isNotEmpty())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('prodiChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [ {!! implode(',', $internshipsByProdi->keys()->map(fn($k) => "'$k'")->toArray()) !!} ],
                datasets: [{
                    data: [ {{ implode(',', $internshipsByProdi->map(fn($v) => $v->count())->toArray()) }} ],
                    backgroundColor: [
                        '#6366F1', // Indigo
                        '#10B981', // Emerald
                        '#F59E0B', // Amber
                        '#EF4444', // Red
                        '#EC4899', // Pink
                        '#3B82F6'  // Blue
                    ],
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
@endif
@endsection
