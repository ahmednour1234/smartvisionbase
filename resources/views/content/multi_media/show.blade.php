@extends('layouts.layoutMaster')

@section('title', __('multi_media.show'))

@section('content')
<div class="card shadow-sm rounded">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-eye me-2"></i> {{ __('multi_media.show') }}</h5>
  </div>

  <div class="card-body">
    <div class="row gy-4">
      {{-- الاسم بالعربي --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('multi_media.name_ar') }}</label>
        <div class="border rounded p-2">{{ $category->name_ar }}</div>
      </div>

      {{-- الاسم بالإنجليزي --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('multi_media.name_en') }}</label>
        <div class="border rounded p-2">{{ $category->name_en }}</div>
      </div>

      {{-- الشعار --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted d-block">{{ __('multi_media.logo') }}</label>
        @if($category->logo)
          <img src="{{ asset($category->logo) }}" alt="logo" class="img-thumbnail" style="width: 120px;">
        @else
          <div class="text-muted">---</div>
        @endif
      </div>

      {{-- الحالة --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('multi_media.active') }}</label>
        <div>
          <span class="badge px-3 py-2 bg-{{ $category->active ? 'success' : 'secondary' }}">
            {{ $category->active ? __('multi_media.status_active') : __('multi_media.status_inactive') }}
          </span>
        </div>
      </div>
    </div>
{{-- رابط الفيديو البرومو --}}
@if(!empty($category->promo))
  <div class="col-12">
    <label class="fw-bold text-muted d-block">{{ __('multi_media.promo_url') }}</label>
    <div class="ratio ratio-16x9 border rounded">
      <iframe
        src="{{ str_contains($category->promo, 'youtube.com') ? str_replace('watch?v=', 'embed/', $category->promo) : $category->promo }}"
        allowfullscreen
        title="Promo Video"
        class="rounded">
      </iframe>
    </div>
  </div>
@endif

    {{-- زر الرجوع --}}
    <div class="mt-4 text-end">
      <a href="{{ route('dashboard.multimedia-categories.index') }}" class="btn btn-outline-primary px-4">
        <i class="bi bi-arrow-left-circle me-1"></i> {{ __('multi_media.back') }}
      </a>
    </div>
  </div>
</div>
@endsection
