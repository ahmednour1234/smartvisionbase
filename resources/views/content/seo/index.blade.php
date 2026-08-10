@extends('layouts.layoutMaster')

@section('title', __('seo_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">{{ __('seo_title') }}</h4>
    <a href="{{ route('dashboard.seos.create') }}" class="btn btn-primary">
      {{ __('seo_create') }}
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>{{ __('seo_route') }}</th>
            <th>{{ __('seo_title_ar') }}</th>
            <th>{{ __('seo_title_en') }}</th>
            <th>{{ __('seo_actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($seos as $seo)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $seo->route }}</td>
            <td>{{ $seo->title['ar'] ?? '-' }}</td>
            <td>{{ $seo->title['en'] ?? '-' }}</td>
            <td>

              <a href="{{ route('dashboard.seos.edit', $seo->id) }}" class="btn btn-sm btn-warning">
                {{ __('seo_edit') }}
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center">{{ __('seo_no_data') }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $seos->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
