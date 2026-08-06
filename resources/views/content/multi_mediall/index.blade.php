@extends('layouts.layoutMaster')

@section('title', __('multi_media.title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>{{ __('multi_media.title') }}</h5>
    <a href="{{ route('dashboard.multi-medias.create') }}" class="btn btn-primary">
      <i class="bi bi-plus"></i> {{ __('multi_media.create') }}
    </a>
  </div>

  <div class="card-body">
    {{-- فلاتر البحث --}}
    <form method="GET" class="row mb-4 g-3">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="{{ __('multi_media.search_name') }}"
               value="{{ request('name') }}">
      </div>

      <div class="col-md-4">
        <input type="date" name="date" class="form-control"
               value="{{ request('date') }}">
      </div>

      <div class="col-md-4">
        <select name="multi_media_category_id" class="form-select">
          <option value="">{{ __('multi_media.all_categories') }}</option>
          @foreach($categories as $id => $name)
            <option value="{{ $id }}" {{ request('multi_media_category_id') == $id ? 'selected' : '' }}>
              {{ $name }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> {{ __('multi_media.search') }}
        </button>
        <a href="{{ route('dashboard.multi-medias.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i> {{ __('multi_media.reset') }}
        </a>
      </div>
    </form>

    {{-- الجدول --}}
    <div class="table-responsive">
      <table class="table align-middle text-center">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('multi_media.name_ar') }}</th>
            <th>{{ __('multi_media.name_en') }}</th>
            <th>{{ __('multi_media.date') }}</th>
            <th>{{ __('multi_media.active') }}</th>
            <th>{{ __('multi_media.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($multiMedias as $media)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $media->name_ar }}</td>
              <td>{{ $media->name_en }}</td>
              <td>{{ $media->date }}</td>
              <td>
                <form action="{{ route('dashboard.multi-medias.activate', $media->id) }}" method="POST">
                  @csrf
                  <button class="btn btn-sm btn-{{ $media->active ? 'success' : 'secondary' }}">
                    {{ $media->active ? __('multi_media.status_active') : __('multi_media.status_inactive') }}
                  </button>
                </form>
              </td>
              <td>
                <a href="{{ route('dashboard.multi-medias.show', $media->id) }}" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i> {{ __('multi_media.show') }}
                </a>
                <a href="{{ route('dashboard.multi-medias.edit', $media->id) }}" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil-square"></i> {{ __('multi_media.edit') }}
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">{{ __('multi_media.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
@if ($multiMedias->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      {{-- السابق --}}
      @if ($multiMedias->onFirstPage())
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $multiMedias->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($multiMedias->getUrlRange(1, $multiMedias->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 text-sm border border-gray-300 {{ $multiMedias->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($multiMedias->hasMorePages())
        <a href="{{ $multiMedias->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
