@extends('layouts.app')

@section('title', 'Statistik Fakultas')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Statistik Magang Fakultas</h4>
            <p class="mb-0">Pemantauan visual sebaran magang program studi di Fakultas.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Dekan</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Statistik</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center p-5">
                <i class="la la-chart-pie text-primary mb-3" style="font-size: 56px;"></i>
                <h4 class="text-dark" style="font-weight: 700;">Diagram &amp; Visualisasi</h4>
                <p class="text-muted">Grafik pie chart dan bar chart pemantauan program magang akan segera terintegrasi dengan Chart.js.</p>
                <a href="{{ route('dekan.dashboard') }}" class="btn btn-primary px-4 mt-2">Kembali ke Dashboard</a>
            </div>
        </div>
    </div>
</div>
@endsection
