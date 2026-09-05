@extends('layouts.app')

@section('title', 'Daftar Program Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Manajemen Program Magang</h4>
            <p class="mb-0">Daftar seluruh program magang mahasiswa.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Kaprodi</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Program Magang</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Search/Filters -->
                <form action="{{ route('kaprodi.internships.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-9">
                        <select name="status" class="form-control">
                            <option value="">Semua Status Magang</option>
                            <option value="waiting_dpl" {{ request('status') === 'waiting_dpl' ? 'selected' : '' }}>Menunggu DPL</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                            <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Diberhentikan / Batal</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">Filter Magang</button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Lowongan Magang</strong></th>
                                <th><strong>Mitra Industri</strong></th>
                                <th><strong>DPL</strong></th>
                                <th><strong>Mulai Magang</strong></th>
                                <th><strong>Status</strong></th>
                                <th><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($internships as $intern)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-dark" style="font-weight: 600;">{{ $intern->student->user->name }}</h6>
                                        <small class="text-muted">NIM: {{ $intern->student->nim }}</small>
                                    </td>
                                    <td>{{ $intern->vacancy->title }}</td>
                                    <td>{{ $intern->vacancy->industry->name }}</td>
                                    <td>
                                        @if($intern->dplAssignment)
                                            <span class="text-dark">{{ $intern->dplAssignment->lecturer->user->name }}</span>
                                        @else
                                            <span class="text-muted fst-italic">Belum diplot</span>
                                        @endif
                                    </td>
                                    <td>{{ $intern->start_date ? \Carbon\Carbon::parse($intern->start_date)->format('d M Y') : '-' }}</td>
                                    <td>
                                        <span class="badge {{ $intern->status_badge['class'] }}">
                                            {{ $intern->status_badge['label'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($intern->status === 'active' || $intern->status === 'waiting_dpl')
                                            <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#cancelModal-{{ $intern->id }}">
                                                Batalkan Magang
                                            </button>
                                            
                                            <!-- Cancel Modal -->
                                            <div class="modal fade" id="cancelModal-{{ $intern->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header border-0">
                                                            <h5 class="modal-title">Berhentikan / Batalkan Magang</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('kaprodi.internships.cancel', $intern->id) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')
                                                            <div class="modal-body text-start">
                                                                <p>Anda yakin ingin memberhentikan magang mahasiswa <strong>{{ $intern->student->user->name }}</strong>?</p>
                                                                <div class="form-group mb-3">
                                                                    <label class="form-label">Alasan Pembatalan <span class="text-danger">*</span></label>
                                                                    <textarea name="reason" class="form-control" rows="3" required placeholder="Cth: Mengundurkan diri, pelanggaran berat, dll"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-0">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                                                <button type="submit" class="btn btn-danger">Konfirmasi Pemberhentian</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Belum ada program magang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $internships->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
