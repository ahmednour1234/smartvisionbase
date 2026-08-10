@extends('layouts.layoutMaster')

@section('title', __('sponsor.page_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('sponsor.page_title') }}</h5>
    <a href="{{ route('admin.sponsors.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ __('sponsor.add') }}
    </a>
  </div>

  <div class="card-body">
    <form method="GET" action="{{ route('admin.sponsors.index') }}" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text" name="name" class="form-control" placeholder="{{ __('sponsor.search_name') }}" value="{{ request('name') }}">
      </div>
      <div class="col-md-4">
        <select name="category_sponsor_id" class="form-select">
          <option value="">{{ __('sponsor.category') }}</option>
          @foreach($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_sponsor_id') == $category->id ? 'selected' : '' }}>
              {{ app()->getLocale() == 'ar' ? $category->name : $category->name_en }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> {{ __('general.search') }}
        </button>
      </div>
    </form>

    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('sponsor.image') }}</th>
            <th>{{ __('sponsor.name_ar') }}</th>
            <th>{{ __('sponsor.name_en') }}</th>
            <th>{{ __('sponsor.category') }}</th>
            <th>{{ __('sponsor.status') }}</th>
            <th>{{ __('sponsor.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($sponsors as $sponsor)
            <tr>
              <td>{{ $loop->iteration + ($sponsors->currentPage() - 1) * $sponsors->perPage() }}</td>
              <td>
                @if($sponsor->image)
                  <img src="{{ asset($sponsor->image) }}" width="60" height="60" class="rounded-circle">
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
              <td>{{ $sponsor->name_ar }}</td>
              <td>{{ $sponsor->name_en }}</td>
              <td>{{ app()->getLocale() == 'ar' ? $sponsor->category->name ?? '-' : $sponsor->category->name_en ?? '-' }}</td>
              <td>
                @if($sponsor->active)
                  <span class="badge bg-success">{{ __('sponsor.active') }}</span>
                @else
                  <span class="badge bg-danger">{{ __('sponsor.inactive') }}</span>
                @endif
              </td>
              <td>
                <a href="{{ route('admin.sponsors.show', $sponsor->id) }}" class="btn btn-sm btn-outline-info" title="{{ __('general.show') }}">
                  <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('admin.sponsors.edit', $sponsor->id) }}" class="btn btn-sm btn-outline-primary" title="{{ __('general.edit') }}">
                  <i class="fas fa-edit"></i>
                </a>

                <a href="{{ route('admin.sponsors.deactivate', $sponsor->id) }}" class="btn btn-sm btn-outline-warning" title="{{ __('general.toggle_status') }}">
                  <i class="fas fa-power-off"></i>
                </a>

                {{-- <form action="{{ route('admin.sponsors.destroy', $sponsor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('general.confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="{{ __('general.delete') }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </form> --}}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">{{ __('general.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
@if ($sponsors->hasPages())
  <div class="mt-4 flex justify-center">
    <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
      {{-- السابق --}}
      @if ($sponsors->onFirstPage())
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
      @else
        <a href="{{ $sponsors->previousPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
      @endif

      {{-- الصفحات --}}
      @foreach ($sponsors->getUrlRange(1, $sponsors->lastPage()) as $page => $url)
        <a href="{{ $url }}"
           class="px-3 py-2 border border-gray-300 {{ $sponsors->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
          {{ $page }}
        </a>
      @endforeach

      {{-- التالي --}}
      @if ($sponsors->hasMorePages())
        <a href="{{ $sponsors->nextPageUrl() }}" class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
      @else
        <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
      @endif
    </nav>
  </div>
@endif

  </div>
</div>
@endsection
