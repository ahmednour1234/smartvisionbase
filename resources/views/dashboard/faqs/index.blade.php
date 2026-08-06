@extends('layouts.dashboard')
@section('title', 'FAQs')
@section('page_title', 'FAQs')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="form-section-title">All FAQs</h2>
            <a href="{{ route('dashboard.faqs.create') }}" class="btn-primary">Add FAQ</a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm dash-card">
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Question</th>
                        <th class="py-2">Active</th>
                        <th class="py-2">Order</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($faqs as $faq)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3">{{ \Illuminate\Support\Str::limit($faq->question, 80) }}</td>
                            <td class="py-3">{{ $faq->is_active ? 'Yes' : 'No' }}</td>
                            <td class="py-3">{{ $faq->sort_order }}</td>
                            <td class="py-3 text-right space-x-2">
                                <a href="{{ route('dashboard.faqs.edit', $faq) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('dashboard.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete FAQ?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-6 text-center text-gray-500" colspan="4">No FAQs found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $faqs->links() }}</div>
            </div>
        </div>
    </div>
@endsection


