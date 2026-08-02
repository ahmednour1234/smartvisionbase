@extends('layouts.layoutMaster')

@section('title', __('speaker.page_title'))

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('speaker.page_title') }}</h5>
    <div class="d-flex gap-2">
      {{-- زر حذف المحدد --}}
      <button form="bulkDeleteForm"
              type="submit"
              class="btn btn-danger me-2"
              onclick="return confirm('{{ __('general.confirm_delete_selected') }}')">
        <i class="fas fa-trash me-1"></i> {{ __('general.delete_selected') }}
      </button>

      {{-- زر إضافة --}}
      <a href="{{ route('admin.speakers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> {{ __('speaker.add') }}
      </a>
    </div>
  </div>

  <div class="card-body">
    {{-- فورم البحث --}}
    <form method="GET" action="{{ route('admin.speakers.index') }}" class="row g-3 mb-4">
      <div class="col-md-4">
        <input type="text"
               name="search"
               class="form-control"
               placeholder="{{ __('speaker.search_name') }}"
               value="{{ request('search') }}">
      </div>
      <div class="col-md-2">
        <button class="btn btn-outline-primary w-100" type="submit">
          <i class="fas fa-search"></i> {{ __('general.search') }}
        </button>
      </div>
    </form>

    <div class="table-responsive">
      {{-- فورم الحذف الجماعي --}}
      <form id="bulkDeleteForm"
            action="{{ route('admin.speakers.destroySelected') }}"
            method="POST">
        @csrf
        @method('DELETE')

        <table class="table align-middle">
          <thead>
            <tr>
              <th>
                <input type="checkbox" id="checkAll">
              </th>
              <th>#</th>
              <th>{{ __('speaker.image') }}</th>
              <th>{{ __('speaker.name') }}</th>
              <th>{{ __('speaker.title') }}</th>
              <th>{{ __('speaker.company') }}</th>
              <th>{{ __('speaker.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($speakers as $index => $speaker)
              <tr>
                {{-- checkbox لكل صف --}}
                <td>
                  <input type="checkbox"
                         name="ids[]"
                         value="{{ $speaker->id }}"
                         class="row-checkbox">
                </td>

                <td>{{ $loop->iteration + ($speakers->currentPage() - 1) * $speakers->perPage() }}</td>

                <td>
                  @if($speaker->image)
                    <img src="{{ asset('public/'.$speaker->image) }}" width="60" height="60" class="rounded-circle">
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>

                <td>{{ app()->getLocale() == 'ar' ? $speaker->name_ar : $speaker->name_en }}</td>
                <td>{{ app()->getLocale() == 'ar' ? $speaker->title_ar : $speaker->title_en }}</td>
                <td>{{ app()->getLocale() == 'ar' ? $speaker->company_name_ar : $speaker->company_name_en }}</td>

                <td>
                  <a href="{{ route('admin.speakers.show', $speaker->id) }}"
                     class="btn btn-sm btn-outline-info"
                     title="{{ __('general.show') }}">
                    <i class="fas fa-eye"></i>
                  </a>

                  <a href="{{ route('admin.speakers.edit', $speaker->id) }}"
                     class="btn btn-sm btn-outline-primary"
                     title="{{ __('general.edit') }}">
                    <i class="fas fa-edit"></i>
                  </a>

                  {{-- لو حابب ترجع الحذف الفردي، لازم يكون برا bulkDeleteForm عشان ما يحصلش تداخل فورمات --}}
                  {{--
                  <form action="{{ route('admin.speakers.destroy', $speaker->id) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('{{ __('speaker.confirm_delete') }}')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                  --}}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center">{{ __('general.no_data') }}</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </form>
    </div>

    {{-- Pagination --}}
    @if ($speakers->hasPages())
      <div class="mt-4 flex justify-center">
        <nav class="inline-flex shadow-sm rounded-md" aria-label="Pagination">
          {{-- السابق --}}
          @if ($speakers->onFirstPage())
            <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-l-md">‹</span>
          @else
            <a href="{{ $speakers->previousPageUrl() }}"
               class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-l-md">‹</a>
          @endif

          {{-- الصفحات --}}
          @foreach ($speakers->getUrlRange(1, $speakers->lastPage()) as $page => $url)
            <a href="{{ $url }}"
               class="px-3 py-2 border border-gray-300 {{ $speakers->currentPage() == $page ? 'bg-primary text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
              {{ $page }}
            </a>
          @endforeach

          {{-- التالي --}}
          @if ($speakers->hasMorePages())
            <a href="{{ $speakers->nextPageUrl() }}"
               class="px-3 py-2 bg-white text-gray-700 border border-gray-300 hover:bg-gray-100 rounded-r-md">›</a>
          @else
            <span class="px-3 py-2 bg-gray-200 text-gray-500 border border-gray-300 rounded-r-md">›</span>
          @endif
        </nav>
      </div>
    @endif

  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
      const checkAll = document.getElementById('checkAll');
      const rowCheckboxes = document.querySelectorAll('.row-checkbox');

      if (!checkAll) return;

      // اختيار/إلغاء اختيار الكل
      checkAll.addEventListener('change', function () {
          rowCheckboxes.forEach(cb => {
              cb.checked = checkAll.checked;
          });
      });

      // تحديث حالة checkAll (checked / indeterminate)
      rowCheckboxes.forEach(cb => {
          cb.addEventListener('change', function () {
              const allChecked = Array.from(rowCheckboxes).every(c => c.checked);
              const anyChecked = Array.from(rowCheckboxes).some(c => c.checked);

              checkAll.checked = allChecked;
              checkAll.indeterminate = !allChecked && anyChecked;
          });
      });
  });
</script>
@endpush
