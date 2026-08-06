@extends('layouts.dashboard')
@section('title', $testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial')
@section('page_title', $testimonial->exists ? 'Edit Testimonial' : 'Add Testimonial')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">{{ $testimonial->exists ? 'Update testimonial' : 'New testimonial' }}</h2>
            <form method="POST" action="{{ $testimonial->exists ? route('dashboard.testimonials.update', $testimonial) : route('dashboard.testimonials.store') }}" class="mt-4 space-y-4" enctype="multipart/form-data">
                @csrf
                @if($testimonial->exists) @method('PUT') @endif
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input name="name" value="{{ old('name', $testimonial->name) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role (optional)</label>
                        <input name="role" value="{{ old('role', $testimonial->role) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('role') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quote</label>
                    <textarea name="quote" rows="4" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('quote', $testimonial->quote) }}</textarea>
                    @error('quote') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Avatar (optional)</label>
                        <input type="file" name="avatar" accept=\"image/*\" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('avatar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    @if($testimonial->avatar)
                        <div class="flex items-center gap-3">
                            <img src=\"{{ asset($testimonial->avatar) }}\" alt=\"avatar\" class=\"h-12 w-12 rounded-full object-cover ring-1 ring-gray-200\">
                            <span class=\"text-xs text-gray-500\">Current</span>
                        </div>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('sort_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2 mt-6 sm:mt-0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $testimonial->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </div>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">{{ $testimonial->exists ? 'Save Changes' : 'Create Testimonial' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection


