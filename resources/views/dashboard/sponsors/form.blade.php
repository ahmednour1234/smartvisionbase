@extends('layouts.dashboard')
@section('title', $sponsor->exists ? 'Edit Sponsor' : 'Add Sponsor')
@section('page_title', $sponsor->exists ? 'Edit Sponsor' : 'Add Sponsor')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">{{ $sponsor->exists ? 'Update sponsor' : 'New sponsor' }}</h2>
            <form method="POST" enctype="multipart/form-data" action="{{ $sponsor->exists ? route('dashboard.sponsors.update', $sponsor) : route('dashboard.sponsors.store') }}" class="mt-4 space-y-4">
                @csrf
                @if($sponsor->exists) @method('PUT') @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name</label>
                    <input name="name" value="{{ old('name', $sponsor->name) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Website URL (optional)</label>
                        <input name="url" value="{{ old('url', $sponsor->url) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="https://..."/>
                        @error('url') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Section</label>
                        <input name="section" value="{{ old('section', $sponsor->section) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g., Our Previous Sponsors"/>
                        @error('section') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Logo</label>
                    <input type="file" name="logo" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    @if($sponsor->logo)
                        <div class="mt-2">
                            <img src="{{ asset($sponsor->logo) }}" alt="" class="h-16 w-auto rounded border">
                        </div>
                    @endif
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $sponsor->sort_order) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('sort_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center gap-2 mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $sponsor->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </div>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">{{ $sponsor->exists ? 'Save Changes' : 'Create Sponsor' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection



