@extends('layouts.app')

@section('title', 'Daftar Bimbingan Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Daftar Mahasiswa Bimbingan Magang</h4>
            <p class="mb-0">Daftar mahasiswa magang aktif dan mahasiswa pra-penempatan yang berada di bawah bimbingan akademik Anda.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Daftar Bimbingan</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <!-- Search & Filters -->
                <form action="{{ route('dpl.students') }}" method="GET" class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="la la-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control border-start-0" placeholder="Cari nama mahasiswa, NIM, posisi, industri..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-control form-select">
                            <option value="">Semua Status Bimbingan</option>
                            <option value="pre_placement" {{ request('status') === 'pre_placement' ? 'selected' : '' }}>Mencari Magang (Pra-Penempatan)</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Berjalan di Industri</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai Magang</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="la la-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status']))
                            <a href="{{ route('dpl.students') }}" class="btn btn-light" title="Reset">
                                <i class="la la-undo"></i>
                            </a>
                        @endif
                        <a href="{{ route('dpl.vacancies.index') }}" class="btn btn-outline-info text-nowrap" title="Lihat Lowongan untuk Bimbingan">
                            <i class="la la-search-location me-1"></i> Cari Lowongan
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-responsive-md table-hover">
                        <thead>
                            <tr>
                                <th><strong>Mahasiswa</strong></th>
                                <th><strong>Tempat Magang / Status</strong></th>
                                <th><strong>Progress / Aktivitas</strong></th>
                                <th><strong>Kontak & Kehadiran</strong></th>
                                <th><strong>Status Bimbingan</strong></th>
                                <th class="text-end"><strong>Aksi</strong></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assign)
                                @php
                                    $intern = $assign->internship;
                                    $student = $assign->student ?? $intern?->student;
                                    $isPrePlacement = is_null($intern);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $student?->photo_url }}" width="38" height="38" class="rounded-circle me-3" style="object-fit: cover;" alt="">
                                            <div>
                                                <h6 class="mb-0 text-dark font-w600">{{ $student?->user?->name ?? 'Mahasiswa' }}</h6>
                                                <small class="text-muted">NIM: {{ $student?->nim ?? '-' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($intern)
                                            <h6 class="mb-0 text-dark font-w600">{{ $intern->vacancy->industry->name }}</h6>
                                            <small class="text-muted">Posisi: {{ $intern->vacancy->title }}</small>
                                        @else
                                            <span class="badge badge-warning text-dark px-2 py-1 mb-1">
                                                <i class="la la-clock me-1"></i> Belum Ditempatkan
                                            </span>
                                            @php
                                                $activeApps = $student?->applications->whereIn('status', ['pending', 'kaprodi_approved']) ?? collect();
                                            @endphp
                                            @if($activeApps->count() > 0)
                                                <small class="d-block text-primary">Sedang melamar {{ $activeApps->count() }} lowongan</small>
                                            @else
                                                <small class="d-block text-muted">Belum ada lamaran aktif</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($intern)
                                            <div style="width: 180px;">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="progress flex-grow-1" style="height: 6px; border-radius: 4px; overflow: hidden; background-color: #E2E8F0;">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $intern->progress_percentage }}%;" aria-valuenow="{{ $intern->progress_percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    <span style="font-size: 12px; font-weight: bold; color: #1E293B;">{{ $intern->progress_percentage }}%</span>
                                                </div>
                                                <small class="text-muted">Mulai: {{ $intern->start_date?->format('d/m/Y') ?? '-' }}</small>
                                            </div>
                                        @else
                                            <span class="badge badge-light text-muted">Persiapan & Rekomendasi</span>
                                            <small class="d-block text-muted mt-1">IPK: {{ $student?->gpa ?? '-' }} &bull; {{ $student?->total_sks ?? 0 }} SKS</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($intern)
                                            <span class="badge bg-primary text-white font-weight-bold px-3 py-2" style="font-size: 12px; border-radius: 6px;">
                                                <i class="la la-calendar-check me-1"></i> {{ $intern->attendances->where('status', 'present')->count() }} Hari Hadir
                                            </span>
                                        @else
                                            <div class="d-flex gap-1">
                                                @if($student?->user?->phone)
                                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $student->user->phone) }}" target="_blank" class="btn btn-outline-success btn-xs" title="Chat WhatsApp">
                                                        <i class="la la-whatsapp"></i> WA
                                                    </a>
                                                @endif
                                                @if($student?->cv_file)
                                                    <a href="{{ $student->cv_url }}" target="_blank" class="btn btn-outline-primary btn-xs" title="Lihat CV">
                                                        <i class="la la-file-pdf"></i> CV
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($intern)
                                            <span class="badge {{ $intern->status_badge['class'] }}">
                                                {{ $intern->status_badge['label'] }}
                                            </span>
                                        @else
                                            <span class="badge badge-info text-white px-2 py-1">
                                                <i class="la la-user-tag me-1"></i> Pra-Penempatan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($isPrePlacement)
                                            <a href="{{ route('dpl.vacancies.index') }}" class="btn btn-outline-info btn-sm" title="Bantu Carikan Lowongan">
                                                <i class="la la-search-location me-1"></i> Rekomendasikan Lowongan
                                            </a>
                                        @else
                                            <a href="{{ route('dpl.logbooks.index', ['student_id' => $student?->id]) }}" class="btn btn-outline-primary btn-sm" title="Logbook Mahasiswa">
                                                <i class="la la-book me-1"></i> Logbook
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="la la-user-friends text-muted fs-1 d-block mb-2"></i>
                                        Belum ada mahasiswa bimbingan magang yang terdaftar untuk Anda.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">
                        Menampilkan {{ $assignments->firstItem() ?? 0 }} - {{ $assignments->lastItem() ?? 0 }} dari {{ $assignments->total() }} mahasiswa bimbingan
                    </small>
                    <div>
                        {{ $assignments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
