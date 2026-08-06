@extends('layouts.layoutMaster')

@section('title', __('ads_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('ads_title') }}</h4>
    <a href="{{ route('dashboard.ads.create') }}" class="btn btn-primary">{{ __('ads_create') }}</a>
  </div>

  <div class="card-body">
    {{-- Filters --}}
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="{{ __('ads_filter_by_name') }}" value="{{ request('name') }}">
      </div>
      <div class="col-md-3">
        <select name="active" class="form-select">
          <option value="">{{ __('ads_filter_by_active') }}</option>
          <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>{{ __('ads_active_yes') }}</option>
          <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>{{ __('ads_active_no') }}</option>
        </select>
      </div>
      <div class="col-md-3">
        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
      </div>
      <div class="col-md-3">
        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-info">{{ __('ads_search') }}</button>
        <a href="{{ route('dashboard.ads.index') }}" class="btn btn-secondary">{{ __('ads_reset') }}</a>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('ads_name') }}</th>
            <th>{{ __('ads_start_date') }}</th>
            <th>{{ __('ads_end_date') }}</th>
            <th>{{ __('ads_active') }}</th>
            <th>{{ __('ads_sort') }}</th>
            <th>{{ __('ads_views') }}</th>
            <th>{{ __('ads_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ads as $ad)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $ad->name[app()->getLocale()] ?? '' }}</td>
            <td>{{ $ad->start_date?->format('Y-m-d') }}</td>
            <td>{{ $ad->end_date?->format('Y-m-d') }}</td>
            <td>
              <span class="badge {{ $ad->active ? 'bg-success' : 'bg-secondary' }}">
                {{ $ad->active ? __('ads_active_yes') : __('ads_active_no') }}
              </span>
            </td>
            <td>{{ $ad->sort }}</td>
            <td>{{ $ad->views }}</td>
            <td>
              <a href="{{ route('dashboard.ads.show', $ad->id) }}" class="btn btn-sm btn-info">
                <i class="bi bi-eye"></i> {{ __('ads_show') }}
              </a>
              <a href="{{ route('dashboard.ads.edit', $ad->id) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil-square"></i> {{ __('ads_edit') }}
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted">{{ __('ads_no_data') }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $ads->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
  </div>
</div>
@endsection
