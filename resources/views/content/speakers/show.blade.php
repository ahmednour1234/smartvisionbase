// resources/views/content/speakers/show.blade.php

@extends('layouts.layoutMaster')

@section('title', app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en)

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <div class="row g-4 align-items-center">
      <div class="col-md-4 text-center">
        @if($speaker->image)
          <img src="{{ asset( $speaker->image) }}" alt="" class="img-thumbnail rounded-circle" width="200">
        @else
          <img src="https://via.placeholder.com/200" class="img-thumbnail rounded-circle" alt="No Image">
        @endif
        <h4 class="mt-3">{{ app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en }}</h4>
        <p class="text-muted">{{ app()->getLocale() === 'ar' ? $speaker->title_ar : $speaker->title_en }}</p>
        @if($speaker->linkedin)
          <a href="{{ $speaker->linkedin }}" class="btn btn-sm btn-outline-primary" target="_blank">
            <i class="fab fa-linkedin"></i> LinkedIn
          </a>
        @endif
      </div>

      <div class="col-md-8">
        <h5 class="mb-3">{{ __('speaker.details') }}</h5>
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <strong>{{ __('speaker.name') }}:</strong>
            {{ app()->getLocale() === 'ar' ? $speaker->name_ar : $speaker->name_en }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('speaker.title') }}:</strong>
            {{ app()->getLocale() === 'ar' ? $speaker->title_ar : $speaker->title_en }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('speaker.company') }}:</strong>
            {{ app()->getLocale() === 'ar' ? $speaker->company_name_ar : $speaker->company_name_en }}
          </li>
          <li class="list-group-item">
            <strong>{{ __('speaker.social_links') }}:</strong>
            <p class="mb-0">{!! nl2br(e($speaker->social_links)) !!}</p>
          </li>
        </ul>
        <a href="{{ route('admin.speakers.index') }}" class="btn btn-outline-secondary mt-4">
          <i class="fas fa-arrow-left me-1"></i> {{ __('general.back') }}
        </a>
      </div>
    </div>
  </div>
</div>
@endsection
