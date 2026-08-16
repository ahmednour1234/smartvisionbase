@extends('layouts.layoutMaster')

@section('title', __('qr_code_list'))

@section('content')
<style>
    .custom-pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin-top: 20px;
    flex-wrap: wrap;
}

.custom-pagination li {
    margin: 0 5px;
}

.custom-pagination li a {
    display: block;
    padding: 8px 14px;
    text-decoration: none;
    background-color: #f0f0f0;
    color: #333;
    border-radius: 6px;
    transition: all 0.3s ease;
    border: 1px solid #ccc;
}

.custom-pagination li a:hover {
    background-color: #2c4470;
    color: #fff;
}

.custom-pagination li.active a {
    background-color: #95bb48;
    color: #fff;
    font-weight: bold;
    border-color: #95bb48;
}

.custom-pagination li.disabled a {
    color: #aaa;
    pointer-events: none;
    background-color: #e0e0e0;
}

</style>
<div class="container">
    <h3>{{ __('qr_code_list') }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" action="{{ route('dashboard.qrcodes.index') }}" class="row g-3 mb-4 align-items-end">
        <div class="col-md-2">
            <input type="number" name="register_id" class="form-control" placeholder="{{ __('register_id') }}"
                   value="{{ request('register_id') }}">
        </div>

    

        <div class="col-md-1">
            <input type="number" name="scan" class="form-control" placeholder="{{ __('scan_count') }}"
                   value="{{ request('scan') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
        </div>

        <div class="col-md-2">
            <input type="text" name="short_code" id="short_code_input" class="form-control" placeholder="{{ __('short_code') }}"
                   value="{{ request('short_code') }}">
        </div>

        <div class="col-md-1">
            <button type="button" class="btn btn-secondary w-100" onclick="startQrScanner()">📷</button>
        </div>

        <div class="col-md-1">
            <button type="submit" class="btn btn-primary w-100">{{ __('filter') }}</button>
        </div>
    </form>

    <div id="qr-reader" style="width:300px; display:none; margin-bottom: 20px;"></div>

    {{-- Table --}}
<div class="table-responsive">
    <table class="table table-hover align-middle text-center border rounded shadow-sm">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{ __('email') }}</th>
                <th scope="col">{{ __('register_id') }}</th>
                <th scope="col">{{ __('scan_count') }}</th>
                <th scope="col">{{ __('status') }}</th>
                <th scope="col">{{ __('created_at') }}</th>
                <th scope="col">{{ __('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qrcodes as $qr)
                <tr>
                    {{-- QR Image --}}
                    <td>
                        <img src="{{ asset('qrcodes/' . $qr->qrcode) }}" width="60" class="rounded border" alt="QR">
                    </td>

                    {{-- Email --}}
                    <td>
                        <span class="text-primary fw-semibold">{{ $qr->email }}</span>
                    </td>

                    {{-- Register ID --}}
                    <td>
                        <span class="badge bg-secondary">{{ $qr->register_id }}</span>
                    </td>

                    {{-- Scan Count --}}
                    <td>
                        <span class="fw-bold text-dark">{{ $qr->scan }}</span>
                    </td>

                    {{-- Status --}}
                    <td>
                        @if($qr->active)
                            <span class="badge bg-success">{{ __('active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('inactive') }}</span>
                        @endif
                    </td>

                    {{-- Created At --}}
                    <td>
                        <span class="text-muted">{{ $qr->created_at->format('Y-m-d') }}</span>
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="{{ route('dashboard.qrcodes.toggle', $qr->id) }}"
                               class="btn btn-sm btn-outline-warning"
                               title="{{ __('toggle') }}">
                                <i class="fas fa-exchange-alt"></i>
                            </a>
                            <a href="{{ route('dashboard.qrcodes.regenerate', $qr->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="{{ __('regenerate_and_resend') }}">
                                <i class="fas fa-redo"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="alert alert-info my-2">
                            {{ __('no_results') }}
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Custom Pagination --}}
@if ($qrcodes->hasPages())
    <ul class="custom-pagination">
        <li class="{{ $qrcodes->onFirstPage() ? 'disabled' : '' }}">
            <a href="{{ $qrcodes->previousPageUrl() ?? '#' }}">&laquo; {{ __('Previous') }}</a>
        </li>

        @for ($i = 1; $i <= $qrcodes->lastPage(); $i++)
            <li class="{{ $qrcodes->currentPage() == $i ? 'active' : '' }}">
                <a href="{{ $qrcodes->url($i) }}">{{ $i }}</a>
            </li>
        @endfor

        <li class="{{ !$qrcodes->hasMorePages() ? 'disabled' : '' }}">
            <a href="{{ $qrcodes->nextPageUrl() ?? '#' }}">{{ __('Next') }} &raquo;</a>
        </li>
    </ul>
@endif

</div>
@endsection

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function startQrScanner() {
        const qrReader = new Html5Qrcode("qr-reader");
        document.getElementById("qr-reader").style.display = "block";

        qrReader.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: 250 },
            (decodedText) => {
                const shortCode = decodedText.split('/qr/')[1] || decodedText;
                document.getElementById("short_code_input").value = shortCode;
                qrReader.stop();
                document.getElementById("qr-reader").style.display = "none";
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        ).catch(err => console.error(err));
    }
</script>
