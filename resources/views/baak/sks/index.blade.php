@extends('layouts.app')

@section('title', 'Validasi SKS Mahasiswa')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Validasi SKS Mahasiswa</h4>
            <p class="mb-0">Verifikasi jumlah SKS yang telah lulus untuk menentukan kelayakan magang akademik.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">BAAK</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Validasi SKS</a></li>
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
                                <th><strong>NIM</strong></th>
                                <th><strong>Nama Mahasiswa</strong></th>
                                <th><strong>Program Studi</strong></th>
                                <th><strong>SKS Lulus</strong></th>
                                <th><strong>Syarat Minimum</strong></th>
                                <th><strong>Status Kelayakan</strong></th>
                                <th><strong>Aksi Simpan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                @php 
                                    $req = $student->requirements->first(); 
                                    $sksCompleted = $req ? $req->sks_completed : $student->total_sks;
                                    $sksMinimum = $req ? $req->sks_minimum : 100;
                                    $eligible = $req ? $req->sks_eligible : ($sksCompleted >= $sksMinimum);
                                @endphp
                                <tr>
                                    <td><strong class="text-dark">{{ $student->nim }}</strong></td>
                                    <td>{{ $student->user->name }}</td>
                                    <td>{{ $student->studyProgram->name }}</td>
                                    
                                    <!-- Input Form Inline -->
                                    <form action="{{ route('baak.sks.update', $student->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <td style="width: 120px;">
                                            <input type="number" name="sks_completed" class="form-control form-control-sm" value="{{ $sksCompleted }}" min="0" required>
                                        </td>
                                        <td style="width: 120px;">
                                            <input type="number" name="sks_minimum" class="form-control form-control-sm" value="{{ $sksMinimum }}" min="0" required>
                                        </td>
                                        <td>
                                            @if($eligible)
                                                <span class="badge badge-success text-white">ELIGIBLE SKS</span>
                                            @else
                                                <span class="badge badge-danger text-white">BELUM ELIGIBLE</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="submit" class="btn btn-primary btn-sm px-3">
                                                <i class="la la-save me-1"></i> Simpan SKS
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada data mahasiswa.</td>
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
