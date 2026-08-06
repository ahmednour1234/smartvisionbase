@extends('layouts.layoutMaster')

@section('title', __('qr_code_list'))

@section('content')
<div class="container py-4">

    {{-- ===== Page Header ===== --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3">
        <h3 class="mb-2 mb-md-0 fw-bold">{{ __('qr_code_list') }}</h3>


    </div>

    {{-- ===== Flash ===== --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm" id="flash-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== Filters ===== --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard.qrcodes.index') }}" class="row g-3 align-items-end">
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('register_id') }}</label>
                    <input type="number" name="register_id" class="form-control"
                           placeholder="{{ __('register_id') }}" value="{{ request('register_id') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('scan_count') }}</label>
                    <input type="number" name="scan" class="form-control"
                           placeholder="{{ __('scan_count') }}" value="{{ request('scan') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('from_date') }}</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('to_date') }}</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-8 col-md-3">
                    <label class="form-label">{{ __('short_code') }}</label>
                    <div class="input-group">
                        <input type="text" name="short_code" id="short_code_input" class="form-control"
                               placeholder="{{ __('short_code') }}" value="{{ request('short_code') }}">
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4">{{ __('filter') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===== Table ===== --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width:100px">#</th>
                            <th>{{ __('email') }}</th>
                            <th>{{ __('register_id') }}</th>
                            <th>{{ __('scan_count') }}</th>
                            <th>{{ __('status') }}</th>
                            <th>{{ __('created_at') }}</th>
                            <th style="width:220px">{{ __('actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($qrcodes as $qr)
                            <tr>
                                <td>
                                    @php
                                        // الأفضل استخدام Storage::url لو المسار محفوظ داخل storage
                                        $img = \Illuminate\Support\Facades\Storage::exists($qr->qrcode ?? '') 
                                            ? \Illuminate\Support\Facades\Storage::url($qr->qrcode) 
                                            : asset('storage/app/public/' . ltrim($qr->qrcode, '/'));
                                    @endphp
                                    <img src="{{ $img }}" alt="QR" class="rounded border" style="width:80px; height:80px; object-fit:contain;">
                                </td>
                                <td>
                                    @if($qr->email)
                                        <a href="mailto:{{ $qr->email }}" class="text-decoration-none">{{ $qr->email }}</a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>{{ $qr->register_id ?? '—' }}</td>
                                <td><span class="badge bg-info-subtle text-info">{{ (int) $qr->scan }}</span></td>
                                <td>
                                    @if($qr->active)
                                        <span class="badge bg-success">{{ __('active') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ optional($qr->created_at)->format('Y-m-d') ?? '—' }}</td>
                                <td class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('dashboard.qrcodes.toggle', $qr->id) }}"
                                       class="btn btn-sm btn-warning"
                                       onclick="return confirm('{{ __('are_you_sure') ?? 'Are you sure?' }}');">
                                        {{ __('toggle') }}
                                    </a>
                                    <a href="{{ route('dashboard.qrcodes.regenerate', $qr->id) }}"
                                       class="btn btn-sm btn-primary"
                                       onclick="return confirm('{{ __('confirm_regenerate_resend') ?? 'Regenerate & resend?' }}');">
                                        {{ __('regenerate_and_resend') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">{{ __('no_results') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ===== Pagination (Native, no libraries) ===== --}}
            @php
                $current = $qrcodes->currentPage();
                $last    = $qrcodes->lastPage();
                $total   = $qrcodes->total();
                $from    = $qrcodes->firstItem();
                $to      = $qrcodes->lastItem();
                $window  = 2; // عدد الأرقام حول الصفحة الحالية
                $start   = max(1, $current - $window);
                $end     = min($last, $current + $window);
            @endphp

            @if($last > 1)
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between px-3 py-3 border-top">
                    <div class="small text-muted mb-2 mb-md-0">
                        {{ __('showing') ?? 'Showing' }} {{ $from ?? 0 }}–{{ $to ?? 0 }} {{ __('of') ?? 'of' }} {{ $total }}
                    </div>

                    <nav aria-label="Pagination">
                        <ul class="pagination mb-0">
                            {{-- First --}}
                            <li class="page-item @if($current===1) disabled @endif">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}" tabindex="-1" aria-disabled="{{ $current===1 ? 'true' : 'false' }}">« {{ __('first') ?? 'First' }}</a>
                            </li>
                            {{-- Prev --}}
                            <li class="page-item @if(!$qrcodes->onFirstPage()) @else disabled @endif">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => max(1, $current-1)]) }}" aria-label="Previous">
                                    ‹ {{ __('previous') ?? 'Previous' }}
                                </a>
                            </li>

                            {{-- Numbers (with gaps) --}}
                            @if($start > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => 1]) }}">1</a>
                                </li>
                                @if($start > 2)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                <li class="page-item @if($i===$current) active @endif">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $i]) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            @if($end < $last)
                                @if($end < $last - 1)
                                    <li class="page-item disabled"><span class="page-link">…</span></li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $last]) }}">{{ $last }}</a>
                                </li>
                            @endif

                            {{-- Next --}}
                            <li class="page-item @if(!$qrcodes->hasMorePages()) disabled @endif">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => min($last, $current+1)]) }}" aria-label="Next">
                                    {{ __('next') ?? 'Next' }} ›
                                </a>
                            </li>
                            {{-- Last --}}
                            <li class="page-item @if($current===$last) disabled @endif">
                                <a class="page-link" href="{{ request()->fullUrlWithQuery(['page' => $last]) }}">{{ __('last') ?? 'Last' }} »
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- ===== QR Scan Modal ===== --}}
<div class="modal fade" id="qrScanModal" tabindex="-1" aria-labelledby="qrScanLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">
      <div class="modal-header">
        <h5 class="modal-title" id="qrScanLabel">{{ __('scan_qr') ?? 'Scan QR' }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('close') ?? 'Close' }}"></button>
      </div>
      <div class="modal-body">
        <div id="qr-reader" style="width:100%; max-width:360px; margin:auto;"></div>
        <div class="text-center mt-3 text-muted small">
            {{ __('point_camera_to_qr') ?? 'Point your camera at the QR code' }}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="stopQrBtn" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ __('close') ?? 'Close' }}</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('page-style')
<style>
    .table-hover tbody tr:hover { background-color: #fafafa; }
    .badge.bg-info-subtle { background-color: #e7f3ff; color: #0b5ed7; }
    #flash-success { transition: opacity .4s ease; }
</style>
@endpush

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    // ===== Flash auto-hide =====
    (function(){
        const el = document.getElementById('flash-success');
        if(el){ setTimeout(()=>{ el.style.opacity = 0; setTimeout(()=>el.remove(), 400); }, 3500); }
    })();

    // ===== QR Scanner (starts/stops with modal) =====
    let qrReader = null;
    const modalEl = document.getElementById('qrScanModal');

    function startQrScanner(){
        if(qrReader) return;
        qrReader = new Html5Qrcode("qr-reader");
        qrReader.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                const shortCode = (decodedText.split('/qr/')[1] || decodedText).trim();
                const input = document.getElementById("short_code_input");
                input.value = shortCode;
                // اغلق المودال وأوقف الكاميرا
                stopQrScanner();
                const modal = bootstrap.Modal.getInstance(modalEl);
                modal?.hide();
            },
            (e) => { /* ignore scan errors */ }
        ).catch(err => console.error(err));
    }

    function stopQrScanner(){
        if(qrReader){
            qrReader.stop().then(()=>{ qrReader.clear(); qrReader = null; })
                    .catch(()=>{ qrReader = null; });
        }
    }

    modalEl.addEventListener('shown.bs.modal', startQrScanner);
    modalEl.addEventListener('hide.bs.modal', stopQrScanner);
</script>
