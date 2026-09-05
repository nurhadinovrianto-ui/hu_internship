@extends('layouts.app')

@section('title', 'Penilaian Akademik DPL')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Penilaian Akademik DPL</h4>
            <p class="mb-0">Input nilai laporan, presentasi sidang, dan keaktifan logbook harian mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Penilaian Akhir</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('dpl.assessment.index') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mahasiswa, NIM, industri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Penilaian</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Belum Dinilai</option>
                            <option value="assessed" {{ request('status') === 'assessed' ? 'selected' : '' }}>Sudah Dinilai</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('dpl.assessment.index') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>Laporan (40%)</strong></th>
                                <th><strong>Presentasi (30%)</strong></th>
                                <th><strong>Logbook (30%)</strong></th>
                                <th><strong>Nilai Akhir</strong></th>
                                <th><strong>Aksi Simpan</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                @php $assess = $intern->assessments->firstWhere('assessor_type', 'dpl'); @endphp
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $intern->student->nim }}</small>
                                    </td>
                                    <td>{{ $intern->vacancy->industry->name }}</td>
                                    
                                    <!-- Form Nilai Akademik DPL -->
                                    <form action="{{ route('dpl.assessment.store', $intern->id) }}" method="POST">
                                        @csrf
                                        
                                        <td style="width: 100px;">
                                            @if($assess)
                                                <span class="text-dark font-weight-bold">{{ $assess->report_score }}</span>
                                            @else
                                                <input type="number" name="report_score" class="form-control form-control-sm" min="0" max="100" required>
                                            @endif
                                        </td>
                                        <td style="width: 100px;">
                                            @if($assess)
                                                <span class="text-dark font-weight-bold">{{ $assess->presentation_score }}</span>
                                            @else
                                                <input type="number" name="presentation_score" class="form-control form-control-sm" min="0" max="100" required>
                                            @endif
                                        </td>
                                        <td style="width: 100px;">
                                            @if($assess)
                                                <span class="text-dark font-weight-bold">{{ $assess->logbook_score }}</span>
                                            @else
                                                <input type="number" name="logbook_score" class="form-control form-control-sm" min="0" max="100" required>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assess)
                                                <span class="badge badge-success text-white font-weight-bold" style="font-size: 13px;">
                                                    {{ $assess->final_score }}
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($assess)
                                                <span class="badge light bg-success text-success font-weight-bold py-2 px-3">TERINILAI</span>
                                            @else
                                                <button type="submit" class="btn btn-primary btn-sm px-3">
                                                    <i class="la la-save me-1"></i> Simpan Nilai
                                                </button>
                                            @endif
                                        </td>
                                    </form>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada mahasiswa magang terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $internships->firstItem() ?? 0 }} - {{ $internships->lastItem() ?? 0 }} dari {{ $internships->total() }} mahasiswa
                    </small>
                    <div>
                        {{ $internships->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
