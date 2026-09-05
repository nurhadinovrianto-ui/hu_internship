@extends('layouts.app')

@section('title', 'Penilaian Sidang Magang')

@section('content')
<div class="row page-titles mx-0">
    <div class="col-sm-6 p-md-0">
        <div class="welcome-text">
            <h4>Lembar Penilaian Sidang Magang</h4>
            <p class="mb-0">Peran Anda: <strong>{{ $roleInDefense }}</strong></p>
        </div>
    </div>
    <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dpl.dashboard') }}">DPL</a></li>
            <li class="breadcrumb-item"><a href="{{ route('dpl.defenses.index') }}">Sidang Magang</a></li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Penilaian</a></li>
        </ol>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Rubrik Penilaian Sidang Seminar</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dpl.defenses.storeAssessment', $defense->id) }}" method="POST" id="form-assessment">
                    @csrf

                    <!-- 1. Presentasi -->
                    <div class="mb-4 border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">1. Kemampuan Presentasi & Penyampaian (Bobot: 30%) <span class="text-danger">*</span></label>
                            <span class="badge light badge-primary" id="badge-p-score">0 / 100</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 12px;">Kejelasan artikulasi, efektivitas media/slide presentasi, ketepatan alokasi waktu, dan sikap profesional saat presentasi.</p>
                        <input type="number" name="presentation_score" id="input-p" class="form-control score-input" min="0" max="100" step="0.1" value="{{ old('presentation_score', $myScore?->presentation_score ?? 80) }}" required>
                    </div>

                    <!-- 2. Penguasaan Materi -->
                    <div class="mb-4 border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">2. Penguasaan Materi & Tanya Jawab (Bobot: 40%) <span class="text-danger">*</span></label>
                            <span class="badge light badge-primary" id="badge-m-score">0 / 100</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 12px;">Ketepatan menjawab pertanyaan penguji, pemahaman proses bisnis mitra, solusi teknis yang dibuat, dan argumentasi ilmiah.</p>
                        <input type="number" name="material_mastery_score" id="input-m" class="form-control score-input" min="0" max="100" step="0.1" value="{{ old('material_mastery_score', $myScore?->material_mastery_score ?? 80) }}" required>
                    </div>

                    <!-- 3. Kualitas Laporan -->
                    <div class="mb-4 border-bottom pb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-bold mb-0">3. Kualitas & Sistematika Laporan Magang (Bobot: 30%) <span class="text-danger">*</span></label>
                            <span class="badge light badge-primary" id="badge-r-score">0 / 100</span>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 12px;">Kerapihan penulisan, kelengkapan lampiran bukti magang, kesesuaian format panduan kampus, dan kedalaman pembahasan hasil kerja.</p>
                        <input type="number" name="report_quality_score" id="input-r" class="form-control score-input" min="0" max="100" step="0.1" value="{{ old('report_quality_score', $myScore?->report_quality_score ?? 80) }}" required>
                    </div>

                    <!-- TOTAL ESTIMASI -->
                    <div class="p-3 bg-light rounded d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="mb-0 text-dark fw-bold">Nilai Rata-rata dari Anda:</h6>
                            <small class="text-muted">(30% Presentasi + 40% Penguasaan + 30% Laporan)</small>
                        </div>
                        <h3 class="text-primary mb-0 fw-bold" id="total-score-display">80.00</h3>
                    </div>

                    <!-- CATATAN REVISI -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Masukan / Poin Revisi Penguji (Jika Ada)</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="Tuliskan catatan perbaikan laporan akhir atau hal yang perlu direvisi mahasiswa...">{{ old('notes', $myScore?->notes ?? $defense->revision_notes) }}</textarea>
                    </div>

                    <!-- STATUS KELULUSAN -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Rekomendasi Hasil Sidang <span class="text-danger">*</span></label>
                        <select name="status" class="form-control form-select">
                            <option value="passed" {{ (old('status', $defense->status) === 'passed') ? 'selected' : '' }}>Lulus Sidang (Nilai Baik)</option>
                            <option value="revision" {{ (old('status', $defense->status) === 'revision') ? 'selected' : '' }}>Lulus dengan Revisi (Diberikan Batas Waktu 7 Hari)</option>
                            <option value="failed" {{ (old('status', $defense->status) === 'failed') ? 'selected' : '' }}>Tidak Lulus (Wajib Sidang Ulang)</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="{{ route('dpl.defenses.index') }}" class="btn btn-light">
                            <i class="la la-arrow-left me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-success px-4">
                            <i class="la la-check me-1"></i> Simpan Nilai Sidang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- RIGHT: STUDENT & INTERNSHIP INFO -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-0 pb-0">
                <h4 class="card-title">Informasi Peserta Sidang</h4>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $defense->student->photo_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $defense->student->user->name }}">
                    <div>
                        <h6 class="mb-0 text-dark fw-bold">{{ $defense->student->user->name }}</h6>
                        <small class="text-muted">NIM: {{ $defense->student->nim }}</small><br>
                        <small class="text-muted">{{ $defense->student->studyProgram?->name }}</small>
                    </div>
                </div>

                <ul class="list-group list-group-flush mb-4">
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Perusahaan Mitra:</small>
                        <strong class="text-dark">{{ $defense->internship->vacancy->industry->name }}</strong>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Posisi Magang:</small>
                        <span class="text-dark">{{ $defense->internship->vacancy->title }}</span>
                    </li>
                    <li class="list-group-item px-0">
                        <small class="text-muted d-block">Jadwal Sidang:</small>
                        <span class="text-dark">{{ $defense->scheduled_date?->format('d M Y') }} ({{ substr($defense->start_time, 0, 5) }} WIB)</span>
                    </li>
                </ul>

                @if($defense->presentation_file_path)
                    <div class="pt-2">
                        <a href="{{ asset('storage/' . $defense->presentation_file_path) }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-2">
                            <i class="la la-file-powerpoint me-1"></i> Unduh Slide Presentasi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const inputP = document.getElementById('input-p');
        const inputM = document.getElementById('input-m');
        const inputR = document.getElementById('input-r');
        const totalDisplay = document.getElementById('total-score-display');

        function calc() {
            const p = parseFloat(inputP.value) || 0;
            const m = parseFloat(inputM.value) || 0;
            const r = parseFloat(inputR.value) || 0;

            document.getElementById('badge-p-score').innerText = p.toFixed(1) + ' / 100';
            document.getElementById('badge-m-score').innerText = m.toFixed(1) + ' / 100';
            document.getElementById('badge-r-score').innerText = r.toFixed(1) + ' / 100';

            const total = (p * 0.30) + (m * 0.40) + (r * 0.30);
            totalDisplay.innerText = total.toFixed(2);
        }

        [inputP, inputM, inputR].forEach(el => el.addEventListener('input', calc));
        calc();
    });
</script>
@endsection
