@extends('layouts.layoutMaster')

@section('title')
  @php
    $isSpecial = (int)($type ?? 1) === 2;
  @endphp
  {{ $isSpecial ? __('speaker.special_guests_title', [], app()->getLocale()) : __('speaker.page_title', [], app()->getLocale()) }}
@endsection

@section('content')
@php
  // تحديد إذا كانت صفحة الضيوف المميزين
  $isSpecial = (int)($type ?? 1) === 2;

  // عناوين الصفحة والأزرار
  $pageTitle = $isSpecial ? __('speaker.special_guests_title') : __('speaker.page_title');
  $addLabel  = $isSpecial ? __('speaker.add_special_guest') : __('speaker.add');

  // الأقسام المتاحة:
  // لو الكنترولر مرّر $sections هنستخدمه، وإلا fallback لقيم افتراضية
  /** @var array<string> $sections */
  $sections = isset($sections) && is_array($sections) && count($sections)
    ? array_values($sections)
    : ['special', 'aps'];

  // القيمة المختارة حاليًا من الـ query
  $currentSection = trim((string) request('section', ''));
  $currentSearch  = trim((string) request('search', ''));
@endphp

<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $pageTitle }}</h5>
    <a href="{{ route('admin.speakers.create', $type) }}" class="btn btn-primary">
      <i class="fas fa-plus me-1"></i> {{ $addLabel }}
    </a>
  </div>

  <div class="card-body">
    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.speakers.index', $type) }}" class="row g-3 mb-4">
      <div class="col-md-4">
        <input
          type="text"
          name="search"
          class="form-control"
          placeholder="{{ __('speaker.search_name') }}"
          value="{{ $currentSearch }}"
        >
      </div>

      @if($isSpecial)
        <div class="col-md-4">
          <select name="section" class="form-select">
            <option value="">{{ __('speaker.choose_section') }}</option>
            @foreach($sections as $sec)
              <option value="{{ $sec }}" {{ $currentSection === $sec ? 'selected' : '' }}>
                {{ __("speaker.$sec") }}
              </option>
            @endforeach
          </select>
        </div>
      @endif

      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> {{ __('general.search') }}
        </button>
      </div>
    </form>

    {{-- لو محتاج Tabs مستقبلاً للـ special/aps ممكن تفعّلها هنا --}}
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>{{ __('speaker.image') }}</th>
            <th>{{ __('speaker.name') }}</th>
            <th>{{ __('speaker.title') }}</th>
            <th>{{ __('speaker.company') }}</th>
            <th>{{ __('speaker.number_of_followers') }}</th>
            <th class="text-end">{{ __('speaker.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse($speakers as $index => $speaker)
            <tr>
              <td>{{ $loop->iteration + ($speakers->currentPage() - 1) * $speakers->perPage() }}</td>

              <td>
                @if($speaker->image)
                  <img
                    src="{{ asset('public/'.$speaker->image) }}"
                    width="60" height="60"
                    class="rounded-circle object-fit-cover"
                    alt="img"
                  >
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>

              <td>{{ app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en }}</td>
              <td>{{ app()->getLocale() === 'ar' ? $speaker->title_ar : $speaker->title_en }}</td>
              <td>{{ app()->getLocale() === 'ar' ? $speaker->company_name_ar : $speaker->company_name_en }}</td>
              <td>{{ $speaker->number_of_followers ?? '' }}</td>

              <td class="text-end text-nowrap">
                <a href="{{ route('admin.speakers.show', [$type, $speaker->id]) }}"
                   class="btn btn-sm btn-outline-info"
                   title="{{ __('general.view') }}">
                  <i class="fas fa-eye"></i>
                </a>

                <a href="{{ route('admin.speakers.edit', [$type, $speaker->id]) }}"
                   class="btn btn-sm btn-outline-primary"
                   title="{{ __('general.edit') }}">
                  <i class="fas fa-edit"></i>
                </a>

                <a href="{{ route('dashboard.speakers.schedule', [$speaker->id]) }}"
                   class="btn btn-sm btn-outline-primary"
                   title="{{ __('speaker.schedule') }}">
                  <i class="fas fa-clock"></i>
                </a>

                <form action="{{ route('admin.speakers.destroy', [$speaker]) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('{{ __('speaker.confirm_delete') }}')">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" title="{{ __('general.delete') }}">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
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

    {{-- Pagination (يحافظ على search & section) --}}
    @php
      $paginator = $speakers->appends(request()->query());
      $current = $paginator->currentPage();
      $last    = $paginator->lastPage();
      $window  = 2;
      $start   = max(1, $current - $window);
      $end     = min($last, $current + $window);

      if ($current <= $window) {
          $end = min($last, max($end, 1 + ($window * 2)));
      }
      if ($current > $last - $window) {
          $start = max(1, min($start, $last - ($window * 2)));
      }

      $range = range($start, $end);
    @endphp

    @if ($paginator->hasPages())
      <div class="mt-4 d-flex justify-content-center">
        <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
          {{-- Prev --}}
          @if ($paginator->onFirstPage())
            <span class="px-3 py-2 bg-light text-muted border rounded-start">‹</span>
          @else
            <a href="{{ $paginator->previousPageUrl() }}"
               class="px-3 py-2 bg-white text-secondary border hover:bg-light rounded-start">‹</a>
          @endif

          {{-- First page --}}
          @if (!in_array(1, $range))
            <a href="{{ $paginator->url(1) }}"
               class="px-3 py-2 border {{ $current === 1 ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light' }}">1</a>
            @if ($start > 2)
              <span class="px-3 py-2 border bg-white text-muted">…</span>
            @endif
          @endif

          {{-- Middle range --}}
          @foreach ($range as $page)
            <a href="{{ $paginator->url($page) }}"
               class="px-3 py-2 border {{ $current === $page ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light' }}">
              {{ $page }}
            </a>
          @endforeach

          {{-- Last page --}}
          @if (!in_array($last, $range))
            @if ($end < $last - 1)
              <span class="px-3 py-2 border bg-white text-muted">…</span>
            @endif
            <a href="{{ $paginator->url($last) }}"
               class="px-3 py-2 border {{ $current === $last ? 'bg-primary text-white' : 'bg-white text-secondary hover:bg-light' }}">{{ $last }}</a>
          @endif

          {{-- Next --}}
          @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
               class="px-3 py-2 bg-white text-secondary border hover:bg-light rounded-end">›</a>
          @else
            <span class="px-3 py-2 bg-light text-muted border rounded-end">›</span>
          @endif
        </nav>
      </div>
    @endif

  </div>
</div>
@endsection
