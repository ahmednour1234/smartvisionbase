@extends('layouts.dashboard')
@section('title', 'Media')
@section('page_title', 'Media Library')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm">
            <form class="flex items-center gap-4" method="POST" action="{{ route('dashboard.media.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" accept="image/*,video/*" class="flex-1">
                <button class="btn-primary rounded-md px-4 py-2 text-sm">Upload</button>
            </form>
            @error('file') <p class="text-sm text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($files as $f)
                <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
                    <div class="aspect-square bg-black/5 flex items-center justify-center">
                        @if(in_array($f['ext'], ['mp4','webm']))
                            <video class="h-full w-full object-cover" controls src="{{ asset($f['path']) }}"></video>
                        @else
                            <img class="h-full w-full object-cover" src="{{ asset($f['path']) }}" alt="">
                        @endif
                    </div>
                    <div class="p-4 flex items-center justify-between text-sm">
                        <span class="truncate">{{ basename($f['path']) }}</span>
                        <form method="POST" action="{{ route('dashboard.media.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="path" value="{{ $f['path'] }}">
                            <button class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-gray-500">No media yet.</div>
            @endforelse
        </div>
    </div>
@endsection


