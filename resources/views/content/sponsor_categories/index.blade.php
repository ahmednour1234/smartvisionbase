
@extends('layouts.layoutMaster')

@section('title', __('sponsor_category.page_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('sponsor_category.page_title') }}</h5>
    <a href="{{ route('admin.sponsor_categories.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ __('sponsor_category.add') }}
    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="{{ route('admin.sponsor_categories.index') }}" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="{{ __('sponsor_category.search_name') }}" value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> {{ __('general.search') }}
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table  align-middle">
        <thead >
          <tr>
            <th>#</th>
            <th>{{ __('sponsor_category.name') }}</th>
            <th>{{ __('sponsor_category.name_en') }}</th>
            <th>{{ __('sponsor_category.status') }}</th>
            <th>{{ __('sponsor_category.logo') }}</th>
            <th>{{ __('sponsor_category.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($categories as $index => $category)
            <tr>
              <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
              <td>{{ $category->name }}</td>
              <td>{{ $category->name_en }}</td>
              <td>
                @if($category->active)
                  <span class="badge bg-success">{{ __('general.active') }}</span>
                @else
                  <span class="badge bg-danger">{{ __('general.inactive') }}</span>
                @endif
              </td>
              <td>
                @if($category->logo)
                  <img src="{{ asset($category->logo) }}" width="60" class="rounded">
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.sponsor_categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-edit"></i>
                </a>
                <a href="{{ route('admin.sponsor_categories.show', $category->id) }}" class="btn btn-sm btn-outline-info">
                  <i class="fas fa-eye"></i>
                </a>
                <form action="{{ route('admin.sponsor_categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('sponsor_category.confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                <a href="{{ route('admin.sponsor_categories.deactivate', $category->id) }}" class="btn btn-sm btn-outline-warning">
                  <i class="fas fa-ban"></i>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center">{{ __('general.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($categories->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
      {{-- السابق --}}
      @if ($categories->onFirstPage())
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $categories->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 border border-gray-300 {{ $categories->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($categories->hasMorePages())
        <a href="{{ $categories->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
