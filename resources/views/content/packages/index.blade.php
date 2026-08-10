@extends('layouts.layoutMaster')

@section('title', __('package.title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5>{{ __('package.title') }}</h5>
    <a href="{{ route('dashboard.packages.create') }}" class="btn btn-primary">
      <i class="bi bi-plus"></i> {{ __('package.create') }}
    </a>
  </div>

  <div class="card-body">
    {{-- Filters --}}
    <form method="GET" class="row mb-4 g-3">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="{{ __('package.name_ar') }}"
               value="{{ request('name') }}">
      </div>

      <div class="col-md-4">
        <input type="number" name="price" class="form-control" placeholder="{{ __('package.price') }}"
               value="{{ request('price') }}">
      </div>

      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> {{ __('multi_media.search') }}
        </button>
        <a href="{{ route('dashboard.packages.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i>  {{ __('multi_media.reset') }}
        </a>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('package.name_ar') }}</th>
 
            <th>{{ __('package.sort') }}</th>
            <th>{{ __('package.active') }}</th>
            <th>{{ __('package.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($packages as $package)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $package->name_ar }}</td>
      
              <td>{{ $package->sort }}</td>
              <td>
                <form action="{{ route('dashboard.packages.activate', $package->id) }}" method="POST">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-{{ $package->active ? 'success' : 'secondary' }}">
                    {{ $package->active ? __('package.status_active') : __('package.status_inactive') }}
                  </button>
                </form>
              </td>
              <td>
                <a href="{{ route('dashboard.packages.show', $package->id) }}" class="btn btn-sm btn-info">👁️</a>
                <a href="{{ route('dashboard.packages.edit', $package->id) }}" class="btn btn-sm btn-warning">✏️</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7">لا توجد بيانات حالياً</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
  @if ($packages->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex rounded-md shadow-sm" aria-label="Pagination">
      {{-- السابق --}}
      @if ($packages->onFirstPage())
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $packages->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($packages->getUrlRange(1, $packages->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 text-sm border border-gray-300 {{ $packages->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($packages->hasMorePages())
        <a href="{{ $packages->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
