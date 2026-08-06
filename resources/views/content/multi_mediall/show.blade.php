@extends('layouts.layoutMaster')

@section('title', __('multi_media.show'))

@section('content')
<div class="card shadow-sm">
  <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
      <i class="bi bi-eye me-2"></i> {{ __('multi_media.show') }} - {{ $media->name_ar }}
    </h5>
    <a href="{{ route('dashboard.multi-medias.index') }}" class="btn btn-outline-light btn-sm">
      <i class="bi bi-arrow-left"></i> {{ __('multi_media.back') }}
    </a>
  </div>

  <div class="card-body">
    <div class="row gy-4">
      {{-- الاسم --}}
      <div class="col-md-6">
        <strong class="text-muted">{{ __('multi_media.name_ar') }}:</strong>
        <div class="border p-2 rounded">{{ $media->name_ar }}</div>
      </div>
      <div class="col-md-6">
        <strong class="text-muted">{{ __('multi_media.name_en') }}:</strong>
        <div class="border p-2 rounded">{{ $media->name_en }}</div>
      </div>

      {{-- التاريخ --}}
      <div class="col-md-6">
        <strong class="text-muted">{{ __('multi_media.date') }}:</strong>
        <div class="border p-2 rounded">{{ $media->date }}</div>
      </div>

      {{-- التصنيف --}}
      <div class="col-md-6">
        <strong class="text-muted">{{ __('multi_media.category') }}:</strong>
        <div class="border p-2 rounded">{{ $media->category?->name_ar ?? '-' }}</div>
      </div>

      {{-- الحالة --}}
      <div class="col-md-6">
        <strong class="text-muted">{{ __('multi_media.active') }}:</strong>
        <div class="mt-1">
          <span class="badge bg-{{ $media->active ? 'success' : 'secondary' }}">
            {{ $media->active ? __('multi_media.status_active') : __('multi_media.status_inactive') }}
          </span>
        </div>
      </div>
    </div>

    {{-- الصور --}}
    @if(!empty($media->images))
      <hr>
      <h5 class="mt-4">{{ __('multi_media.images') }}</h5>
      <div class="row">
        @foreach($media->images as $image)
          <div class="col-md-3 col-6 mb-3">
            <div class="border rounded p-1 h-100 text-center">
              <img src="{{asset($image) }}" alt="image" class="img-fluid rounded shadow-sm" style="max-height: 180px;">
            </div>
          </div>
        @endforeach
      </div>
    @endif

    {{-- الفيديوهات --}}
@php
    function convertToEmbed($url) {
        if (str_contains($url, 'watch?v=')) {
            return str_replace('watch?v=', 'embed/', $url);
        }
        return $url;
    }
@endphp

@if(!empty($media->links))
  <hr>
  <h5 class="mt-4">{{ __('multi_media.links') }}</h5>
  <div class="row">
    @foreach($media->links as $link)
      <div class="col-md-6 mb-3">
        <div class="ratio ratio-16x9 border rounded">
          <iframe src="{{ convertToEmbed($link) }}" title="video" allowfullscreen></iframe>
        </div>
      </div>
    @endforeach
  </div>
@endif

  </div>
</div>
@endsection
