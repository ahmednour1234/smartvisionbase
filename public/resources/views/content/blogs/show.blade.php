@extends('layouts.layoutMaster')

@section('title', __('blog.show'))

@section('content')
<div class="card shadow-sm rounded">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="bi bi-eye me-2"></i> {{ __('blog.show') }}</h5>
  </div>

  <div class="card-body">
    <div class="row gy-4">
      {{-- الاسم بالعربية --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('blog.name_ar') }}</label>
        <div class="border rounded p-2">{{ $blog->name_ar }}</div>
      </div>

      {{-- الاسم بالإنجليزية --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('blog.name_en') }}</label>
        <div class="border rounded p-2">{{ $blog->name_en }}</div>
      </div>

      {{-- العنوان بالعربية --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('blog.title_ar') }}</label>
        <div class="border rounded p-2">{{ $blog->title_ar }}</div>
      </div>

      {{-- العنوان بالإنجليزية --}}
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('blog.title_en') }}</label>
        <div class="border rounded p-2">{{ $blog->title_en }}</div>
      </div>

      {{-- الوصف بالعربية --}}
      <div class="col-md-12">
        <label class="fw-bold text-muted">{{ __('blog.description_ar') }}</label>
        <div class="border rounded p-2">{{ $blog->description_ar }}</div>
      </div>

      {{-- الوصف بالإنجليزية --}}
      <div class="col-md-12">
        <label class="fw-bold text-muted">{{ __('blog.description_en') }}</label>
        <div class="border rounded p-2">{{ $blog->description_en }}</div>
      </div>

      {{-- الصورة --}}
      @if($blog->image)
      <div class="col-md-6">
        <label class="fw-bold text-muted">{{ __('blog.image') }}</label>
        <div class="border rounded p-2">
          <img src="{{ asset($blog->image) }}" alt="Blog Image" class="img-fluid rounded" style="max-width: 100%; height: auto;">
        </div>
      </div>
      @endif

      {{-- الترتيب --}}
      <div class="col-md-3">
        <label class="fw-bold text-muted">{{ __('blog.sort') }}</label>
        <div class="border rounded p-2">{{ $blog->sort }}</div>
      </div>

      {{-- التاريخ --}}
      <div class="col-md-3">
        <label class="fw-bold text-muted">{{ __('blog.date') }}</label>
        <div class="border rounded p-2">{{ $blog->date }}</div>
      </div>

      {{-- الحالة --}}
      <div class="col-md-3">
        <label class="fw-bold text-muted">{{ __('blog.active') }}</label>
        <div class="border rounded p-2">
          <span class="badge bg-{{ $blog->active ? 'success' : 'secondary' }}">
            {{ $blog->active ? __('blog.status_active') : __('blog.status_inactive') }}
          </span>
        </div>
      </div>
    </div>

    {{-- زر الرجوع --}}
    <div class="mt-4 text-end">
      <a href="{{ route('dashboard.blogs.index') }}" class="btn btn-outline-secondary px-4">
        <i class="bi bi-arrow-left"></i> {{ __('blog.back') }}
      </a>
    </div>
  </div>
</div>
@endsection
