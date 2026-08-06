{{-- resources/views/content/galleries/edit.blade.php --}}
@extends('layouts.layoutMaster')
@section('title', __('gallery.edit'))

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ __('gallery.edit') }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('dashboard.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('content.galleries._form', ['isEdit' => true])

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {{ __('gallery.update') }}
                </button>
                <a href="{{ route('dashboard.galleries.index') }}" class="btn btn-outline-secondary ms-2">
                    {{ __('gallery.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
