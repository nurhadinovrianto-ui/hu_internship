@extends('layouts.app')

@section('title', 'Review Jurnal Logbook')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Review Logbook Mahasiswa</h4>
            <p class="mb-0">Evaluasi aktivitas harian praktikan magang dan berikan komentar bimbingan.</p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">DPL</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dpl.logbooks.index') }}">Logbook</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Review</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <!-- Card 1: Logbook Detail (Left Column) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card mb-4">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <span class="text-primary font-weight-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Aktivitas Jurnal</span>
                <span class="badge {{ $logbook->status_badge['class'] }} py-2 px-3 float-end" style="font-size: 11px; font-weight: 600;">
                    {{ $logbook->status_badge['label'] }}
                </span>
            </div>
            <div class="card-body">
                <!-- Info Praktikan -->
                <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3 mr-3" style="width: 50px; height: 50px; font-size: 20px; font-weight: 700; min-width: 50px;">
                        {{ strtoupper(substr($logbook->student->user->name ?? 'M', 0, 1)) }}
                    </div>
                    <div>
                        <h6 class="mb-0 text-dark" style="font-weight: 700; font-size: 15px;">{{ $logbook->student->user->name }}</h6>
                        <span class="text-muted" style="font-size: 13px;">
                            NIM: <strong>{{ $logbook->student->nim }}</strong> &bull; 
                            <i class="la la-calendar ms-1 ml-1"></i> {{ $logbook->date->translatedFormat('d F Y') }}
                        </span>
                    </div>
                </div>

                <h5 class="text-dark mb-3" style="font-weight: 700; line-height: 1.4;">{{ $logbook->title }}</h5>

                <!-- Deskripsi Aktivitas -->
                <div class="mb-4">
                    <h6 class="text-primary mb-2" style="font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">
                        <i class="la la-tasks me-2 mr-2"></i>DESKRIPSI AKTIVITAS
                    </h6>
                    <div class="p-3 rounded bg-light border" style="color: #334155; font-size: 13.5px; line-height: 1.6; background-color: #fcfcfc;">
                        {!! nl2br(e($logbook->description)) !!}
                    </div>
                </div>

                <!-- Hasil Pembelajaran -->
                @if($logbook->learning_outcomes)
                    <div class="mb-4">
                        <h6 class="text-primary mb-2" style="font-weight: 700; font-size: 13px; letter-spacing: 0.5px;">
                            <i class="la la-lightbulb-o me-2 mr-2"></i>HASIL PEMBELAJARAN
                        </h6>
                        <div class="p-3 rounded border" style="color: #1e293b; font-size: 13.5px; line-height: 1.6; background-color: #f4f7fe; border-color: rgba(74, 114, 227, 0.15) !important;">
                            {!! nl2br(e($logbook->learning_outcomes)) !!}
                        </div>
                    </div>
                @endif
                
                <!-- Berkas Lampiran -->
                @if($logbook->attachment)
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="text-dark mb-2" style="font-weight: 700; font-size: 13px;">
                            <i class="la la-paperclip me-1 mr-1"></i> LAMPIRAN
                        </h6>
                        <div class="p-3 rounded border bg-light d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center">
                                <i class="la la-file-alt text-primary me-2 mr-2" style="font-size: 24px;"></i>
                                <span class="text-dark font-weight-600" style="font-size: 13px;">Berkas Lampiran</span>
                            </div>
                            <a href="{{ asset('storage/' . $logbook->attachment) }}" target="_blank" class="btn btn-primary btn-xs px-2 py-1">
                                <i class="la la-download"></i> Unduh
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Card 2: Timeline Riwayat Review (Middle Column) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        <div class="card mb-4">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <span class="text-primary font-weight-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Riwayat Evaluasi</span>
                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 10px; font-weight: 600;">
                    {{ $logbook->reviews->count() + 1 }} Aktivitas
                </span>
            </div>
            <div class="card-body">
                <h5 class="text-dark mb-4" style="font-weight: 700; line-height: 1.4;">
                    <i class="la la-history text-primary me-1 mr-1"></i> Jejak &amp; Riwayat Review
                </h5>
                <div class="widget-timeline">
                    <ul class="timeline">
                        <!-- Step 1: Mahasiswa Mengirim -->
                        <li>
                            <div class="timeline-badge primary"></div>
                            <div class="timeline-panel text-muted">
                                <span>{{ $logbook->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                <h6 class="mb-1 text-dark" style="font-weight: 700; font-size: 13.5px;">Jurnal Dikirim oleh Mahasiswa</h6>
                                <p class="mb-0 text-muted" style="font-size: 12.5px;">
                                    Laporan jurnal harian berhasil diserahkan oleh <strong>{{ $logbook->student->user->name ?? 'Mahasiswa' }}</strong>.
                                </p>
                            </div>
                        </li>

                        <!-- Steps 2+: Review dari DPL / Industri -->
                        @forelse($logbook->reviews->sortBy('created_at') as $rev)
                            @php
                                $isApproved = ($rev->status === 'approved');
                                $isRevision = ($rev->status === 'revision');
                                $badgeColorClass = $isApproved ? 'success' : ($isRevision ? 'danger' : 'warning');
                                $statusText = $isApproved ? 'DISETUJUI' : ($isRevision ? 'PERLU REVISI' : 'DICATAT');
                                $accentColor = $isApproved ? '#10B981' : ($isRevision ? '#EF4444' : '#F59E0B');
                            @endphp
                            <li>
                                <div class="timeline-badge {{ $badgeColorClass }}"></div>
                                <div class="timeline-panel text-muted">
                                    <span>{{ $rev->created_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    <div class="d-flex align-items-center flex-wrap mb-1 mt-1">
                                        <h6 class="mb-0 text-dark me-2 mr-2" style="font-weight: 700; font-size: 13.5px;">{{ $rev->reviewer->name }}</h6>
                                        <span class="badge badge-xs bg-{{ $badgeColorClass }} text-white" style="font-size: 10px; font-weight: 600;">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                    <span class="text-muted d-block mb-2" style="font-size: 11px;">
                                        {{ $rev->reviewer_type === 'dpl' ? 'DPL Kampus' : 'Pembimbing Industri' }}
                                    </span>
                                    <div class="p-2 rounded bg-white text-dark mt-1" style="font-size: 13px; line-height: 1.5; border-left: 3px solid {{ $accentColor }}; background-color: #fafafa; border: 1px solid #eee; border-left: 3px solid {{ $accentColor }} !important;">
                                        <span class="font-italic">"{{ $rev->comment }}"</span>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li>
                                <div class="timeline-badge dark"></div>
                                <div class="timeline-panel text-muted">
                                    <h6 class="mb-1 text-muted" style="font-weight: 600; font-size: 13.5px;">Belum Ada Evaluasi</h6>
                                    <p class="mb-0" style="font-size: 12.5px;">Menunggu masukan penilaian dari DPL maupun Industri.</p>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Review Form (Right Column) -->
    <div class="col-xl-4 col-lg-4 col-md-12">
        @php
            $myReview = $logbook->reviews->where('reviewer_type', 'dpl')->first();
        @endphp
        <div class="card mb-4 sticky-top" style="top: 95px; z-index: 10;">
            <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                <span class="text-primary font-weight-bold text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">Form Evaluasi</span>
                <span class="badge {{ $myReview ? 'bg-warning text-white' : 'bg-primary text-white' }} py-1 px-2" style="font-size: 10px; font-weight: 600;">
                    {{ $myReview ? 'Mode Edit' : 'Review Baru' }}
                </span>
            </div>
            <div class="card-body">
                <h5 class="text-dark mb-4" style="font-weight: 700; line-height: 1.4;">
                    @if($myReview)
                        <i class="la la-edit text-warning me-1 mr-1"></i> Perbarui Review DPL
                    @else
                        <i class="la la-check-circle text-primary me-1 mr-1"></i> Beri Review DPL
                    @endif
                </h5>

                <form action="{{ route('dpl.logbooks.review', $logbook->id) }}" method="POST">
                    @csrf
                    
                    <div class="form-group mb-3">
                        <label class="form-label text-dark font-weight-bold" for="status">Keputusan Peninjauan</label>
                        <select name="status" id="status" class="form-control" style="height: 44px; border-radius: 6px; font-size: 13.5px;" required>
                            <option value="approved" {{ ($myReview?->status === 'approved') ? 'selected' : '' }}>Setujui Logbook (Approved)</option>
                            <option value="noted" {{ ($myReview?->status === 'noted') ? 'selected' : '' }}>Beri Catatan (Noted)</option>
                            <option value="revision" {{ ($myReview?->status === 'revision') ? 'selected' : '' }}>Minta Revisi (Revision)</option>
                        </select>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label text-dark font-weight-bold" for="comment">Catatan Evaluasi / Komentar</label>
                        <textarea name="comment" id="comment" class="form-control" rows="5" style="border-radius: 6px; font-size: 13.5px; line-height: 1.6;" placeholder="Tulis instruksi atau masukan bimbingan kerja..." required>{{ $myReview?->comment }}</textarea>
                    </div>

                    @if($myReview)
                        <button type="submit" class="btn btn-warning text-white btn-block btn-lg shadow-xs" style="font-weight: 600; font-size: 14px; border-radius: 6px;">
                            <i class="la la-refresh me-1 mr-1"></i> Perbarui Catatan Review
                        </button>
                    @else
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-xs" style="font-weight: 600; font-size: 14px; border-radius: 6px;">
                            <i class="la la-save me-1 mr-1"></i> Simpan Review Jurnal
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
