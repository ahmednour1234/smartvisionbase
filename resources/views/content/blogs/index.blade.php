@extends('layouts.layoutMaster')

@section('title', __('blog.title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('blog.title') }}</h5>
    <a href="{{ route('dashboard.blogs.create') }}" class="btn btn-primary">
      <i class="bi bi-plus"></i> {{ __('blog.create') }}
    </a>
  </div>

  <div class="card-body">

    {{-- Filters --}}
    <form method="GET" class="row g-3 mb-4">
      <div class="col-md-4">
        <input
          type="text"
          name="name"
          class="form-control"
          placeholder="{{ __('blog.name_ar') }} / {{ __('blog.name_en') }}"
          value="{{ request('name') }}"
        >
      </div>
      <div class="col-md-4">
        <input
          type="date"
          name="date"
          class="form-control"
          value="{{ request('date') }}"
        >
      </div>
      <div class="col-md-4 text-end">
        <button type="submit" class="btn btn-outline-primary">
          <i class="bi bi-search"></i> {{ __('general.search') }}
        </button>
        <a href="{{ route('dashboard.blogs.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-x-circle"></i> {{ __('general.reset') }}
        </a>
      </div>
    </form>

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table text-center align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('blog.name_ar') }}</th>
            <th>{{ __('blog.name_en') }}</th>
            <th>{{ __('blog.date') }}</th>
            <th>{{ __('blog.active') }}</th>
            <th>{{ __('blog.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($blogs as $blog)
            <tr>
              <td>{{ ($blogs->currentPage() - 1) * $blogs->perPage() + $loop->iteration }}</td>
              <td class="text-wrap">{{ $blog->name_ar }}</td>
              <td class="text-wrap">{{ $blog->name_en }}</td>
              <td>{{ $blog->date }}</td>

              {{-- Activate/Deactivate --}}
              <td>
                <form action="{{ route('dashboard.blogs.activate', $blog->id) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-{{ $blog->active ? 'success' : 'secondary' }}">
                    {{ $blog->active ? __('blog.status_active') : __('blog.status_inactive') }}
                  </button>
                </form>
              </td>

              {{-- Actions --}}
              <td>
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('dashboard.blogs.show', $blog->id) }}" class="btn btn-sm btn-info" title="{{ __('general.show') }}">👁️</a>
                  <a href="{{ route('dashboard.blogs.edit', $blog->id) }}" class="btn btn-sm btn-warning" title="{{ __('general.edit') }}">✏️</a>

                  {{-- Destroy --}}
                  <form action="{{ route('dashboard.blogs.destroy', $blog->id) }}" method="POST"
                        onsubmit="return confirm('{{ __('general.confirm_delete') ?? 'Are you sure you want to delete?' }}');"
                        class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('general.delete') }}">🗑️</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6">{{ __('general.no_data') }}</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if ($blogs->hasPages())
      <div class="mt-4 d-flex justify-content-center">
        <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
          {{-- Prev --}}
          @if ($blogs->onFirstPage())
            <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-start">‹</span>
          @else
            <a href="{{ $blogs->previousPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-start">‹</a>
          @endif

          {{-- Pages --}}
          @foreach ($blogs->getUrlRange(1, $blogs->lastPage()) as $page => $url)
            <a href="{{ $url }}"
               class="px-3 py-2 text-sm border border-gray-300 {{ $blogs->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
               {{ $page }}
            </a>
          @endforeach

          {{-- Next --}}
          @if ($blogs->hasMorePages())
            <a href="{{ $blogs->nextPageUrl() }}" class="px-3 py-2 text-sm bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-end">›</a>
          @else
            <span class="px-3 py-2 text-sm bg-gray-200 text-gray-500 border border-gray-300 rounded-end">›</span>
          @endif
        </nav>
      </div>
    @endif

  </div>
</div>

{{-- Optional: translate fallback for confirm if key missing --}}
<script>
  // If you prefer a custom message based on data-attribute, you can enhance here.
</script>
@endsection
