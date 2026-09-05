@extends('layouts.app')

@section('title', 'Plotting DPL Mahasiswa')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Plotting Dosen Pembimbing Lapangan (DPL)</h4>
            <p class="mb-0">Tugaskan DPL untuk membimbing mahasiswa magang aktif yang telah diterima industri.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Plotting DPL</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Lowongan Magang</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Pilih Dosen Pembimbing (DPL)</strong></th>
                                <th><strong>Tugaskan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $internship)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $internship->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $internship->student->nim }}</small>
                                    </td>
                                    <td>{{ $internship->vacancy->title }}</td>
                                    <td>{{ $internship->vacancy->industry->name }}</td>
                                    
                                    <!-- Form Plotting DPL -->
                                    <form action="{{ route('kaprodi.dpl-plotting.assign', $internship->id) }}" method="POST">
                                        @csrf
                                        
                                        <td style="min-width: 280px;">
                                            <select name="lecturer_id" class="form-control form-control-sm" required>
                                                <option value="">Pilih DPL...</option>
                                                @foreach($lecturers as $lec)
                                                    <option value="{{ $lec['id'] }}" {{ !$lec['has_capacity'] ? 'disabled' : '' }}>
                                                        {{ $lec['name'] }} (Bimbingan: {{ $lec['current_mentee'] }}/{{ $lec['max_mentee'] }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                                <i class="la la-user-plus me-1"></i> Tugaskan DPL
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada mahasiswa magang baru yang menunggu plotting DPL.</td>
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
