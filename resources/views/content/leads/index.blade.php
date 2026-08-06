@extends('layouts.layoutMaster')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-primary">{{ __('leads') }}</h2>

    <form method="GET" class="row g-3 mb-4 align-items-end bg-light p-3 rounded shadow-sm">
        <div class="col-md-3">
            <label class="form-label">{{ __('date') }} (من)</label>
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('date') }} (إلى)</label>
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('campaign') }}</label>
            <input type="text" name="campaign" class="form-control" value="{{ request('campaign') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">{{ __('email') }}</label>
            <input type="text" name="email" class="form-control" value="{{ request('email') }}">
        </div>
        <div class="col-12 text-end">
            <button class="btn btn-success">{{ __('filter') }}</button>
        </div>
    </form>

    <div class="mb-3 bg-white rounded p-3 shadow-sm">
        <div class="row text-center">
            <div class="col-md-4">
                <strong>{{ __('total_leads') }}:</strong> {{ $stats['total_leads'] }}
            </div>
            <div class="col-md-4">
                <strong>{{ __('today_leads') }}:</strong> {{ $stats['today_leads'] }}
            </div>
            <div class="col-md-4">
                <strong>{{ __('unique_campaigns') }}:</strong> {{ $stats['unique_campaigns'] }}
            </div>
        </div>
    </div>

    <div class="table-responsive bg-white rounded shadow-sm">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>{{ __('name') }}</th>
                    <th>{{ __('phone') }}</th>
                    <th>{{ __('email') }}</th>
                    <th>{{ __('campaign') }}</th>
                    <th>{{ __('date') }}</th>
                    <th class="text-center">{{ __('view') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td>{{ $lead->id }}</td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ $lead->email }}</td>
                    <td>{{ $lead->campaign }}</td>
                    <td>{{ $lead->created_at->format('Y-m-d') }}</td>
                    <td class="text-center">
                        <a href="{{ route('dashboard.leads.show', $lead->id) }}" class="btn btn-sm btn-info">{{ __('view') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">No leads found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $leads->withQueryString()->links() }}
    </div>
</div>
@endsection
