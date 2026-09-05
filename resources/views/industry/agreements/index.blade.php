@extends('layouts.app')

@section('title', 'Kelola Internship Agreement')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Internship Agreement & Kontrak Magang</h4>
            <p class="mb-0">Kelola perjanjian kerja sama magang mahasiswa, unggah dokumen kontrak resmi, atau cetak template perjanjian.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Industri</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Internship Agreement</a></li>
        </ol>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="la la-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="la la-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong>Periksa kembali input Anda:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter & Search Card -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('industry.agreements.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="la la-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Cari Nama Mahasiswa atau NIM..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-control">
                    <option value="">-- Semua Status Kontrak --</option>
                    <option value="has_agreement" {{ request('status') === 'has_agreement' ? 'selected' : '' }}>Sudah Ada Kontrak</option>
                    <option value="no_agreement" {{ request('status') === 'no_agreement' ? 'selected' : '' }}>Belum Ada Kontrak</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="la la-filter me-1"></i> Filter</button>
                <a href="{{ route('industry.agreements.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="la la-refresh"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header border-0 pb-0">
        <h4 class="card-title">Daftar Kontrak Mahasiswa Magang</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-responsive-md table-hover align-middle">
                <thead>
                    <tr>
                        <th><strong>Mahasiswa</strong></th>
                        <th><strong>Posisi / Divisi</strong></th>
                        <th><strong>Periode Magang</strong></th>
                        <th><strong>Status Kontrak</strong></th>
                        <th><strong>Dokumen</strong></th>
                        <th class="text-end"><strong>Aksi</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($internships as $item)
                        @php
                            $agreement = $item->agreement;
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h6 class="mb-0 text-dark font-weight-bold">{{ $item->student->user->name ?? '-' }}</h6>
                                        <small class="text-muted">NIM: {{ $item->student->nim ?? '-' }} &bull; {{ $item->student->studyProgram->name ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="d-block font-weight-bold text-dark">{{ $item->vacancy->title ?? '-' }}</span>
                                <small class="text-muted">{{ $item->vacancy->division ?? $item->vacancy->position ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="d-block text-dark font-weight-bold">{{ $item->academicPeriod->name ?? '-' }}</span>
                                <span class="d-block text-muted small">
                                    {{ $item->start_date ? $item->start_date->format('d M Y') : '-' }} s/d 
                                    {{ $item->end_date ? $item->end_date->format('d M Y') : '-' }}
                                </span>
                                @if($agreement && $agreement->allowance)
                                    <small class="text-success"><i class="la la-money-bill me-1"></i>{{ $agreement->allowance }}</small>
                                @endif
                            </td>
                            <td>
                                @if($agreement)
                                    @php $badge = $agreement->status_badge; @endphp
                                    <span class="badge {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    @if($agreement->agreement_number)
                                        <small class="d-block text-muted mt-1">No: {{ $agreement->agreement_number }}</small>
                                    @endif
                                @else
                                    <span class="badge badge-light text-muted">Belum Ada Kontrak</span>
                                @endif
                            </td>
                            <td>
                                @if($agreement && $agreement->document_file)
                                    <a href="{{ asset('storage/' . $agreement->document_file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="la la-file-pdf me-1"></i> Unduh Berkas
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    <!-- Tombol Cetak Draf Template SPK -->
                                    <a href="{{ route('industry.agreements.template', $item->id) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Cetak Template Kontrak">
                                        <i class="la la-print"></i> Cetak Draf
                                    </a>

                                    <!-- Tombol Buat/Edit Modal -->
                                    <button type="button" class="btn btn-sm {{ $agreement ? 'btn-warning' : 'btn-primary' }}" data-bs-toggle="modal" data-bs-target="#agreementModal{{ $item->id }}">
                                        <i class="la {{ $agreement ? 'la-edit' : 'la-plus' }}"></i> {{ $agreement ? 'Edit' : 'Buat Kontrak' }}
                                    </button>

                                    <!-- Tombol Hapus jika ada agreement -->
                                    @if($agreement)
                                        <form action="{{ route('industry.agreements.destroy', $agreement->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus Kontrak">
                                                <i class="la la-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                <!-- Modal Buat/Edit Agreement -->
                                <div class="modal fade text-start" id="agreementModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form action="{{ route('industry.agreements.store', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ $agreement ? 'Edit' : 'Buat' }} Internship Agreement - {{ $item->student->user->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Nomor Kontrak / SPK</label>
                                                            <input type="text" name="agreement_number" class="form-control" placeholder="Contoh: SPK/2026/MG-001" value="{{ old('agreement_number', $agreement?->agreement_number ?? '') }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Judul Agreement <span class="text-danger">*</span></label>
                                                            <input type="text" name="title" class="form-control" required value="{{ old('title', $agreement?->title ?? 'Perjanjian Kerja Sama Magang') }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Tanggal Mulai Kontrak</label>
                                                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $agreement?->start_date ? \Carbon\Carbon::parse($agreement->start_date)->format('Y-m-d') : ($item->start_date ? \Carbon\Carbon::parse($item->start_date)->format('Y-m-d') : '')) }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Tanggal Selesai Kontrak</label>
                                                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $agreement?->end_date ? \Carbon\Carbon::parse($agreement->end_date)->format('Y-m-d') : ($item->end_date ? \Carbon\Carbon::parse($item->end_date)->format('Y-m-d') : '')) }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Fasilitas / Uang Saku (Allowance)</label>
                                                            <input type="text" name="allowance" class="form-control" placeholder="Contoh: Rp 1.500.000 / bulan atau Makan Siang" value="{{ old('allowance', $agreement?->allowance ?? '') }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label font-weight-bold">Status Kontrak <span class="text-danger">*</span></label>
                                                            <select name="status" class="form-control" required>
                                                                <option value="active" {{ old('status', $agreement?->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif / Disepakati</option>
                                                                <option value="draft" {{ old('status', $agreement?->status ?? '') === 'draft' ? 'selected' : '' }}>Draft</option>
                                                                <option value="completed" {{ old('status', $agreement?->status ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
                                                                <option value="terminated" {{ old('status', $agreement?->status ?? '') === 'terminated' ? 'selected' : '' }}>Dihentikan</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label font-weight-bold">Unggah File Agreement Resmi (PDF / DOCX)</label>
                                                            <input type="file" name="document_file" class="form-control" accept=".pdf,.doc,.docx">
                                                            @if($agreement && $agreement->document_file)
                                                                <small class="text-muted d-block mt-1">File saat ini: <a href="{{ asset('storage/' . $agreement->document_file) }}" target="_blank">Lihat Dokumen</a> (biarkan kosong jika tidak ingin mengganti file)</small>
                                                            @endif
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label font-weight-bold">Catatan / Hak & Kewajiban Ringkas</label>
                                                            <textarea name="notes" class="form-control" rows="3" placeholder="Catatan tambahan mengenai hak, kewajiban, atau tata tertib magang...">{{ old('notes', $agreement->notes ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary"><i class="la la-save me-1"></i> Simpan Agreement</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="la la-folder-open d-block mb-2" style="font-size: 36px;"></i>
                                Belum ada data mahasiswa magang yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $internships->links() }}
        </div>
    </div>
</div>
@endsection
