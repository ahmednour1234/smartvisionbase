@extends('layouts.layoutMaster')

@section('title', __('event_schedule.page_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('event_schedule.page_title') }}</h5>
    <a href="{{ route('admin.event_schedules.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ __('event_schedule.add') }}
    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="{{ route('admin.event_schedules.index') }}" class="row g-3 mb-4">
      <div class="col-md-3">
        <input type="text" name="name" class="form-control" placeholder="{{ __('event_schedule.name') }}" value="{{ request('name') }}">
      </div>
      <div class="col-md-3">
        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
      </div>
      <div class="col-md-3">
        <input type="time" name="time" class="form-control" value="{{ request('time') }}">
      </div>
      <div class="col-md-3">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> {{ __('general.search') }}
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('event_schedule.name') }}</th>
            <th>{{ __('event_schedule.event') }}</th>
            <th>{{ __('event_schedule.end_time') }}</th>
            <th>{{ __('event_schedule.start_time') }}</th>
            <th>{{ __('event_schedule.active') }}</th>
            <th>{{ __('event_schedule.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($schedules as $schedule)
            <tr>
              <td>{{ $loop->iteration + ($schedules->currentPage() - 1) * $schedules->perPage() }}</td>
              <td>{{  app()->getLocale() == 'ar' ?$schedule->title_ar ??'-':$schedule->title_em }}</td>
              <td>{{ app()->getLocale() == 'ar' ? $schedule->event->name_ar ?? '-' : $schedule->event->name_en ?? '-' }}</td>
              <td>{{ $schedule->start_datetime }}</td>
              <td>{{ $schedule->end_datetime }}</td>
              <td>
                @if($schedule->active)
                  <span class="badge bg-success">{{ __('event_schedule.active') }}</span>
                @else
                  <span class="badge bg-danger">{{ __('event_schedule.inactive') }}</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.event_schedules.show', $schedule->id) }}" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('admin.event_schedules.edit', $schedule->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('admin.event_schedules.destroy', $schedule->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('general.confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center">{{ __('general.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
@if ($schedules->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      {{-- السابق --}}
      @if ($schedules->onFirstPage())
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $schedules->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($schedules->getUrlRange(1, $schedules->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 text-sm border border-gray-300 {{ $schedules->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($schedules->hasMorePages())
        <a href="{{ $schedules->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
