@extends('layouts.app')

@section('title', 'Validasi SPP Mahasiswa')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Validasi SPP Mahasiswa</h4>
            <p class="mb-0">Verifikasi pelunasan uang kuliah mahasiswa sebelum diizinkan mendaftar magang.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Finance</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Validasi SPP</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('finance.payments.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama mahasiswa atau NIM..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control">
                            <option value="">Semua Status Pembayaran</option>
                            <option value="cleared" {{ request('status') === 'cleared' ? 'selected' : '' }}>Lunas</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending / Belum Lunas</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary btn-block">Filter</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>NIM</strong></th>
                                <th><strong>Nama Mahasiswa</strong></th>
                                <th><strong>Program Studi</strong></th>
                                <th><strong>Status Pembayaran</strong></th>
                                <th><strong>Aksi Verifikasi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php 
                                    $req = $student->requirements->first(); 
                                    $cleared = $req ? $req->payment_cleared : false;
                                @endphp
                                <tr>
                                    <td><strong class="text-dark">{{ $student->nim }}</strong></td>
                                    <td>{{ $student->user->name }}</td>
                                    <td>{{ $student->studyProgram->name }}</td>
                                    <td>
                                        @if($cleared)
                                            <span class="badge badge-success text-white font-weight-bold">
                                                LUNAS
                                            </span>
                                        @else
                                            <span class="badge badge-danger text-white font-weight-bold">
                                                PENDING / BELUM LUNAS
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$cleared)
                                            <form action="{{ route('finance.payments.validate', $student->id) }}" method="POST" style="display: inline-block;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success text-white btn-sm px-3">
                                                    <i class="la la-check me-1"></i> Validasi Lunas
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('finance.payments.revoke', $student->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Membatalkan pelunasan mahasiswa ini?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3">
                                                    <i class="la la-times me-1"></i> Batalkan Lunas
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada data mahasiswa ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
