@if(isset($periods) && $periods->isNotEmpty())
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #ffffff 0%, #f8faff 100%);">
    <div class="card-body py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center">
            <div class="rounded-circle bg-primary text-white p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; flex-shrink: 0;">
                <i class="la la-calendar-check" style="font-size: 22px;"></i>
            </div>
            <div>
                <h6 class="mb-0 text-dark font-weight-bold" style="font-size: 15px;">
                    Periode Akademik: 
                    <span class="text-primary">{{ $period ? $period->name : 'Semua Periode Magang' }}</span>
                    @if($period && $period->is_active)
                        <span class="badge badge-success text-white badge-sm ms-2"><i class="la la-check-circle me-1"></i>Periode Aktif</span>
                    @elseif($period)
                        <span class="badge badge-secondary text-white badge-sm ms-2">Arsip / Non-aktif</span>
                    @endif
                </h6>
                <small class="text-muted">
                    @if($period)
                        Pelaksanaan Magang: {{ $period->start_date->format('d M Y') }} s/d {{ $period->end_date->format('d M Y') }}
                    @else
                        Menampilkan seluruh data statistik kumulatif tanpa filter periode.
                    @endif
                </small>
            </div>
        </div>

        <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2 m-0">
            <label for="period_id_select" class="form-label mb-0 text-muted font-weight-bold text-nowrap" style="font-size: 13px;">
                <i class="la la-filter me-1"></i>Filter Periode:
            </label>
            <select name="period_id" id="period_id_select" class="form-control form-select form-select-sm" style="min-width: 220px; font-weight: 600; border-radius: 8px;" onchange="this.form.submit()">
                <option value="all" {{ (isset($selectedPeriodId) && $selectedPeriodId === 'all') ? 'selected' : '' }}>-- Semua Periode Magang --</option>
                @foreach($periods as $p)
                    <option value="{{ $p->id }}" {{ (isset($selectedPeriodId) && (string)$selectedPeriodId === (string)$p->id) ? 'selected' : '' }}>
                        {{ $p->name }} {{ $p->is_active ? '★ (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
            @if(isset($selectedPeriodId) && $selectedPeriodId !== 'all')
                <a href="{{ url()->current() }}?period_id=all" class="btn btn-outline-secondary btn-sm text-nowrap" title="Tampilkan Semua Periode">
                    <i class="la la-undo"></i>
                </a>
            @endif
        </form>
    </div>
</div>
@endif
