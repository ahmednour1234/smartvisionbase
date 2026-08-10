{{-- resources/views/content/galleries/index.blade.php --}}
@extends('layouts.layoutMaster')
@section('title', __('gallery.title'))

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">{{ __('gallery.title') }}</h5>
    </div>
    <div class="card-body">
        <div class="mb-3 text-end">
            <a href="{{ route('dashboard.galleries.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {{ __('gallery.add') }}
            </a>
        </div>

        @if ($galleries->count())
            <div class="row">
                @foreach ($galleries as $gallery)
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border rounded shadow-sm">
                            <img src="{{ asset($gallery->image) }}" class="card-img-top" alt="image" style="object-fit:cover;height:200px">
                            <div class="card-body p-2 text-center">
                                <form action="{{ route('dashboard.galleries.destroy', $gallery->id) }}" method="POST" onsubmit="return confirm('{{ __('gallery.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-100">
                                        <i class="bi bi-trash"></i> {{ __('gallery.delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $galleries->links() }}
            </div>
        @else
            <p class="text-muted text-center">{{ __('gallery.no_data') }}</p>
        @endif
    </div>
</div>
@endsection
