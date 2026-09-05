@extends('layouts.app')

@section('title', 'Statistik Program Studi')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Statistik &amp; Pemantauan Program Studi</h4>
            <p class="mb-0">Daftar mahasiswa magang aktif beserta instansi penugasan dan dosen pembimbing.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Statistik</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Sebaran Industri Mitra -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Sebaran Mitra Magang</h4>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 280px;">
                @if($byIndustry->isNotEmpty())
                    <canvas id="industryChart" style="max-height: 250px;"></canvas>
                @else
                    <p class="text-muted mb-0">Belum ada sebaran mahasiswa di industri.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Sebaran Status Magang -->
    <div class="col-xl-6 col-lg-6 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Status Program Magang</h4>
            </div>
            <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 280px;">
                @if($byStatus->isNotEmpty())
                    <canvas id="statusChart" style="max-height: 250px; max-width: 250px;"></canvas>
                @else
                    <p class="text-muted mb-0">Belum ada data status magang.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- List Mahasiswa Magang Aktif -->
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Daftar Pemantauan Magang Aktif</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Dosen Pembimbing (DPL)</strong></th>
                                <th><strong>Masa Magang</strong></th>
                                <th><strong>Status Magang</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $intern->student->nim }}</small>
                                    </td>
                                    <td>{{ $intern->vacancy->industry->name }}</td>
                                    <td>{{ $intern->dplAssignment ? $intern->dplAssignment->lecturer->user->name : 'Menunggu DPL' }}</td>
                                    <td>{{ $intern->start_date ? $intern->start_date->format('d/m/Y') : '-' }} - {{ $intern->end_date ? $intern->end_date->format('d/m/Y') : '-' }}</td>
                                    <td>
                                        <span class="badge {{ $intern->status_badge['class'] }}">
                                            {{ $intern->status_badge['label'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Belum ada mahasiswa magang terdaftar.</td>
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
        @if($byIndustry->isNotEmpty())
        const ctxIndustry = document.getElementById('industryChart').getContext('2d');
        new Chart(ctxIndustry, {
            type: 'bar',
            data: {
                labels: [ {!! implode(',', $byIndustry->keys()->map(fn($k) => "'$k'")->toArray()) !!} ],
                datasets: [{
                    label: 'Mahasiswa Magang',
                    data: [ {{ implode(',', $byIndustry->map(fn($v) => $v->count())->toArray()) }} ],
                    backgroundColor: '#6366F1',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
        @endif

        @if($byStatus->isNotEmpty())
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: [
                    @foreach($byStatus->keys() as $status)
                        @if($status === 'waiting_dpl') 'Menunggu DPL',
                        @elseif($status === 'active') 'Aktif Berjalan',
                        @elseif($status === 'completed') 'Selesai Magang',
                        @else '{{ ucfirst($status) }}',
                        @endif
                    @endforeach
                ],
                datasets: [{
                    data: [ {{ implode(',', $byStatus->map(fn($v) => $v->count())->toArray()) }} ],
                    backgroundColor: ['#F59E0B', '#10B981', '#3B82F6', '#EF4444']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
        @endif
    });
</script>
@endsection
