@extends('layouts.dashboard')
@section('title', 'Sponsors')
@section('page_title', 'Sponsors')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="form-section-title">All Sponsors</h2>
            <a href="{{ route('dashboard.sponsors.create') }}" class="btn-primary">Add Sponsor</a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm dash-card">
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Logo</th>
                        <th class="py-2">Name</th>
                        <th class="py-2">Section</th>
                        <th class="py-2">Active</th>
                        <th class="py-2">Order</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($sponsors as $s)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3">
                                @if($s->logo)
                                    <img src="{{ asset($s->logo) }}" alt="" class="h-8 w-auto">
                                @endif
                            </td>
                            <td class="py-3">{{ \Illuminate\Support\Str::limit($s->name, 60) }}</td>
                            <td class="py-3 text-gray-600">{{ $s->section }}</td>
                            <td class="py-3">{{ $s->is_active ? 'Yes' : 'No' }}</td>
                            <td class="py-3">{{ $s->sort_order }}</td>
                            <td class="py-3 text-right space-x-2">
                                <a href="{{ route('dashboard.sponsors.edit', $s) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('dashboard.sponsors.destroy', $s) }}" class="inline" onsubmit="return confirm('Delete sponsor?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-6 text-center text-gray-500" colspan="6">No sponsors found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $sponsors->links() }}</div>
            </div>
        </div>
    </div>
@endsection



