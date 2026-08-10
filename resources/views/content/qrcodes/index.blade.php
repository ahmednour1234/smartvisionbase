@extends('layouts.layoutMaster')

@section('title', __('qr_code_list'))

@section('content')
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
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('email') }}</th>
                <th>{{ __('register_id') }}</th>
                <th>{{ __('scan_count') }}</th>
                <th>{{ __('status') }}</th>
                <th>{{ __('created_at') }}</th>
                <th>{{ __('actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qrcodes as $qr)
                <tr>
                    <td><img src="{{ asset('public/qrcodes/' . $qr->qrcode) }}" width="80"></td>
                    <td>{{ $qr->email }}</td>
                    <td>{{ $qr->register_id }}</td>
                    <td>{{ $qr->scan }}</td>
                    <td>
                        @if($qr->active)
                            <span class="badge bg-success">{{ __('active') }}</span>
                        @else
                            <span class="badge bg-danger">{{ __('inactive') }}</span>
                        @endif
                    </td>
                    <td>{{ $qr->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route('dashboard.qrcodes.toggle', $qr->id) }}" class="btn btn-sm btn-warning">{{ __('toggle') }}</a>
                        <a href="{{ route('dashboard.qrcodes.regenerate', $qr->id) }}" class="btn btn-sm btn-primary">{{ __('regenerate_and_resend') }}</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">{{ __('no_results') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
      @if ($qrcodes->hasPages())
        <nav>
          <ul class="pagination justify-content-center">
            {{-- Previous --}}
            @if ($qrcodes->onFirstPage())
              <li class="page-item disabled"><span class="page-link">‹</span></li>
            @else
              <li class="page-item">
                <a class="page-link" href="{{ $qrcodes->previousPageUrl() }}" rel="prev">‹</a>
              </li>
            @endif

            {{-- Pages --}}
            @foreach ($qrcodes->getUrlRange(1, $qrcodes->lastPage()) as $page => $url)
              <li class="page-item {{ $qrcodes->currentPage() == $page ? 'active' : '' }}">
                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
              </li>
            @endforeach

            {{-- Next --}}
            @if ($qrcodes->hasMorePages())
              <li class="page-item">
                <a class="page-link" href="{{ $qrcodes->nextPageUrl() }}" rel="next">›</a>
              </li>
            @else
              <li class="page-item disabled"><span class="page-link">›</span></li>
            @endif
          </ul>
        </nav>
      @endif
    </div>
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
