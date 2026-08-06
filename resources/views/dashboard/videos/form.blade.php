@extends('layouts.dashboard')
@section('title', $video->exists ? 'Edit Video' : 'Add Video')
@section('page_title', $video->exists ? 'Edit Video' : 'Add Video')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">{{ $video->exists ? 'Update video' : 'New video' }}</h2>
            <form method="POST" action="{{ $video->exists ? route('dashboard.videos.update', $video) : route('dashboard.videos.store') }}" class="mt-4 space-y-4">
                @csrf
                @if($video->exists) @method('PUT') @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input name="title" value="{{ old('title', $video->title) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">YouTube URL</label>
                    <input name="youtube_url" value="{{ old('youtube_url', $video->youtube_url) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://www.youtube.com/watch?v=... or https://youtu.be/..."/>
                    @error('youtube_url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Section (optional)</label>
                        <input name="section" value="{{ old('section', $video->section) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Highlights, Interviews"/>
                        @error('section') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $video->sort_order) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('sort_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $video->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">Active</span>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">{{ $video->exists ? 'Save Changes' : 'Create Video' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection


