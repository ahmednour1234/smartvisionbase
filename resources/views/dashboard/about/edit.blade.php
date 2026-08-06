@extends('layouts.dashboard')
@section('title', 'About Us')
@section('page_title', 'About Us')

@section('content')
    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('dashboard.about.update') }}" class="space-y-8" enctype="multipart/form-data">
            @csrf
            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Titles</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title (AR)</label>
                        <input name="title_ar" value="{{ old('title_ar', $about->title_ar) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('title_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title (EN)</label>
                        <input name="title_en" value="{{ old('title_en', $about->title_en) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('title_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Content</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Content (AR)</label>
                        <textarea name="content_ar" rows="8" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('content_ar', $about->content_ar) }}</textarea>
                        @error('content_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-1">
                        <label class="block text-sm font-medium text-gray-700">Content (EN)</label>
                        <textarea name="content_en" rows="8" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('content_en', $about->content_en) }}</textarea>
                        @error('content_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Media & Visibility</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Upload Image</label>
                        <input type="file" name="image_upload" accept=".png,.jpg,.jpeg,.webp,.svg" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @if($about->image)
                            <div class="mt-3 flex items-center gap-3">
                                <span class="text-xs text-gray-600">Current:</span>
                                <img src="{{ asset($about->image) }}" alt="About Image" class="h-12 w-auto object-contain border rounded-md p-1 bg-white">
                            </div>
                        @endif
                        @error('image_upload') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        <p class="text-xs text-gray-500 mt-1">Optional: You can still provide a path below instead of uploading.</p>
                        <label class="block text-sm font-medium text-gray-700 mt-3">Image Path (optional)</label>
                        <input name="image" value="{{ old('image', $about->image) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $about->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </div>
                </div>
            </div>

            <div>
                <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Save About
                </button>
            </div>
        </form>
    </div>
@endsection


