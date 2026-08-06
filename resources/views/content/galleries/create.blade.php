{{-- resources/views/content/galleries/create.blade.php --}}
@extends('layouts.layoutMaster')
@section('title', __('gallery.add'))

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ __('gallery.add') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('dashboard.galleries.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            @include('content.galleries._form', ['isEdit' => false])

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-upload"></i> {{ __('gallery.upload') }}
                </button>
                <a href="{{ route('dashboard.galleries.index') }}" class="btn btn-outline-secondary ms-2">
                    {{ __('gallery.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
