@extends('layouts.layoutMaster')

@section('title', __('multi_media.title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('multi_media.title') }}</h5>
    <a href="{{ route('dashboard.multimedia-categories.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> {{ __('multi_media.create') }}
    </a>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table  align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('multi_media.name_ar') }}</th>
            <th>{{ __('multi_media.name_en') }}</th>
            <th>{{ __('multi_media.logo') }}</th>
            <th>{{ __('multi_media.active') }}</th>
            <th>{{ __('multi_media.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $category)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $category->name_ar }}</td>
              <td>{{ $category->name_en }}</td>
              <td>
                @if($category->logo)
                  <img src="{{ asset($category->logo) }}" alt="logo" width="50" height="50">
                @else
                  ---
                @endif
              </td>
              <td>
                <form method="POST" action="{{ route('dashboard.multimedia-categories.activate', $category->id) }}">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-{{ $category->active ? 'success' : 'secondary' }}">
                    {{ $category->active ? __('multi_media.status_active') : __('multi_media.status_inactive') }}
                  </button>
                </form>
              </td>
              <td>
                <a href="{{ route('dashboard.multimedia-categories.show', $category->id) }}" class="btn btn-info btn-sm">
                  {{ __('multi_media.show') }}
                </a>
                <a href="{{ route('dashboard.multimedia-categories.edit', $category->id) }}" class="btn btn-warning btn-sm">
                  {{ __('multi_media.edit') }}
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">لا توجد بيانات</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- pagination --}}
 @if ($categories->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      {{-- السابق --}}
      @if ($categories->onFirstPage())
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $categories->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 text-sm border border-gray-300 {{ $categories->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($categories->hasMorePages())
        <a href="{{ $categories->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
