@extends('layouts.dashboard')
@section('title', 'Testimonials')
@section('page_title', 'Testimonials')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="form-section-title">All Testimonials</h2>
            <a href="{{ route('dashboard.testimonials.create') }}" class="btn-primary">Add Testimonial</a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm dash-card">
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Name</th>
                        <th class="py-2">Role</th>
                        <th class="py-2">Active</th>
                        <th class="py-2">Order</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($testimonials as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 flex items-center gap-2">
                                @if($t->avatar)
                                    <img src="{{ asset($t->avatar) }}" alt="{{ $t->name }}" class="h-7 w-7 rounded-full object-cover ring-1 ring-gray-200">
                                @endif
                                {{ $t->name }}
                            </td>
                            <td class="py-3 text-gray-600">{{ $t->role }}</td>
                            <td class="py-3">{{ $t->is_active ? 'Yes' : 'No' }}</td>
                            <td class="py-3">{{ $t->sort_order }}</td>
                            <td class="py-3 text-right space-x-2">
                                <a href="{{ route('dashboard.testimonials.edit', $t) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('dashboard.testimonials.destroy', $t) }}" class="inline" onsubmit="return confirm('Delete testimonial?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-6 text-center text-gray-500" colspan="5">No testimonials found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $testimonials->links() }}</div>
            </div>
        </div>
    </div>
@endsection


