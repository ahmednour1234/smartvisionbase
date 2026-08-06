@extends('layouts.dashboard')
@section('title', 'Events')
@section('page_title', 'Events')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-4">
            <a href="{{ route('dashboard.events.create') }}" class="inline-flex items-center rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">
                Create Event
            </a>
        </div>
        <div class="rounded-xl border bg-white shadow-sm">
            <div class="px-5 py-4 border-b flex items-center justify-between">
                <h2 class="text-sm font-semibold">All Events</h2>
                <span class="text-sm text-gray-500">{{ $events->total() }} total</span>
            </div>
            <div class="p-5 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2">Name</th>
                            <th class="py-2">Dates</th>
                            <th class="py-2">Active</th>
                            <th class="py-2">Featured</th>
                            <th class="py-2" colspan="2"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($events as $event)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 font-medium">{{ $event->name }}</td>
                                <td class="py-3 text-gray-600">
                                    @if($event->start_at) {{ $event->start_at->format('M d, Y') }} @endif
                                    @if($event->end_at) - {{ $event->end_at->format('M d, Y') }} @endif
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('dashboard.events.toggle-active', $event) }}" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $event->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $event->is_active ? 'Active' : 'Inactive' }}
                                    </a>
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('dashboard.events.toggle-featured', $event) }}" class="inline-flex items-center rounded-full px-2 py-0.5 text-xs {{ $event->is_featured ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $event->is_featured ? 'Featured' : 'Normal' }}
                                    </a>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('dashboard.events.edit', $event) }}" class="inline-flex items-center justify-center rounded-md p-2 text-indigo-600 hover:bg-indigo-50" title="Edit">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536M4 20h4.586a1 1 0 00.707-.293l9.9-9.9a2 2 0 000-2.828l-1.072-1.072a2 2 0 00-2.828 0l-9.9 9.9A1 1 0 004 15.414V20z"/>
                                        </svg>
                                    </a>
                                </td>
                                <td class="py-3">
                                    <form method="POST" action="{{ route('dashboard.events.destroy', $event) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center justify-center rounded-md p-2 text-red-600 hover:bg-red-50" title="Delete" onclick="return confirm('Delete this event?')">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m-7 0h8a1 1 0 001-1V5a1 1 0 00-1-1h-3.5a1 1 0 01-.894-.553L10.618 2.5a1 1 0 00-.894-.5H9a1 1 0 00-.894.553L7.5 3.5A1 1 0 016.606 4H3a1 1 0 00-1 1v1a1 1 0 001 1h2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="py-6 text-center text-gray-500" colspan="6">No events yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection


