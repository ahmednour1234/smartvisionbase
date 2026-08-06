@extends('layouts.dashboard')
@section('title', 'Videos')
@section('page_title', 'Videos')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="form-section-title">All Videos</h2>
            <a href="{{ route('dashboard.videos.create') }}" class="btn-primary">Add Video</a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm dash-card">
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                    <tr class="text-left text-gray-500">
                        <th class="py-2">Title</th>
                        <th class="py-2">Section</th>
                        <th class="py-2">Active</th>
                        <th class="py-2">Order</th>
                        <th class="py-2 text-right">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y">
                    @forelse($videos as $v)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3">{{ \Illuminate\Support\Str::limit($v->title, 60) }}</td>
                            <td class="py-3 text-gray-600">{{ $v->section }}</td>
                            <td class="py-3">{{ $v->is_active ? 'Yes' : 'No' }}</td>
                            <td class="py-3">{{ $v->sort_order }}</td>
                            <td class="py-3 text-right space-x-2">
                                <a href="{{ route('dashboard.videos.edit', $v) }}" class="text-indigo-600 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('dashboard.videos.destroy', $v) }}" class="inline" onsubmit="return confirm('Delete video?');">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td class="py-6 text-center text-gray-500" colspan="5">No videos found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $videos->links() }}</div>
            </div>
        </div>
    </div>
@endsection


