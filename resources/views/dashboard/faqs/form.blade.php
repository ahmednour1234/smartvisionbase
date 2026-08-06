@extends('layouts.dashboard')
@section('title', $faq->exists ? 'Edit FAQ' : 'Add FAQ')
@section('page_title', $faq->exists ? 'Edit FAQ' : 'Add FAQ')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
            <h2 class="form-section-title">{{ $faq->exists ? 'Update FAQ' : 'New FAQ' }}</h2>
            <form method="POST" action="{{ $faq->exists ? route('dashboard.faqs.update', $faq) : route('dashboard.faqs.store') }}" class="mt-4 space-y-4">
                @csrf
                @if($faq->exists) @method('PUT') @endif
                <div>
                    <label class="block text-sm font-medium text-gray-700">Question</label>
                    <input name="question" value="{{ old('question', $faq->question) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                    @error('question') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Answer</label>
                    <textarea name="answer" rows="5" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('answer', $faq->answer) }}</textarea>
                    @error('answer') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('sort_order') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 flex items-center gap-2 mt-6 sm:mt-0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $faq->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm text-gray-700">Active</span>
                    </div>
                </div>
                <div class="pt-2">
                    <button class="btn-primary">{{ $faq->exists ? 'Save Changes' : 'Create FAQ' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection


