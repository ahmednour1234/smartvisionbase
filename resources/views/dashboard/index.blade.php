@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page_title', 'Overview')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="rounded-xl border bg-white p-5 shadow-sm dash-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Upcoming Events</div>
                        <div class="mt-2 text-3xl font-semibold">{{ $upcomingCount }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-indigo-50 text-indigo-600 inline-flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3M3 11h18M5 19h14a2 2 0 002-2v-6H3v6a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border bg-white p-5 shadow-sm dash-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Active Events</div>
                        <div class="mt-2 text-3xl font-semibold">{{ $activeCount }}</div>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-emerald-50 text-emerald-600 inline-flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="rounded-xl border bg-white p-5 shadow-sm dash-card">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-sm text-gray-500">Next Event</div>
                        <div class="mt-2 text-lg font-medium">
                            @if($next)
                                {{ $next->title }} <span class="text-gray-500">— {{ $next->start_at?->format('M d, Y') }}</span>
                            @else
                                <span class="text-gray-500">No upcoming event</span>
                            @endif
                        </div>
                    </div>
                    <div class="h-12 w-12 rounded-lg bg-amber-50 text-amber-600 inline-flex items-center justify-center">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 rounded-xl border bg-white shadow-sm dash-card">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <h2 class="text-sm font-semibold">Recent Contact Messages</h2>
                    <a class="text-sm text-indigo-600 hover:underline" href="{{ route('dashboard.contacts.index') }}">View all</a>
                </div>
                <div class="p-5 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500">
                                <th class="py-2">Name</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Subject</th>
                                <th class="py-2">Date</th>
                                <th class="py-2 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($lastMessages as $msg)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3">{{ $msg->name }}</td>
                                    <td class="py-3 text-gray-600">{{ $msg->email }}</td>
                                    <td class="py-3">{{ \Illuminate\Support\Str::limit($msg->subject, 40) }}</td>
                                    <td class="py-3 text-gray-600">{{ $msg->created_at->diffForHumans() }}</td>
                                    <td class="py-3 text-right">
                                        <a href="{{ route('dashboard.contacts.show', $msg) }}" class="text-indigo-600 hover:underline">Open</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="py-6 text-center text-gray-500" colspan="5">No messages yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="rounded-xl border bg-white p-5 shadow-sm dash-card">
                <h2 class="text-sm font-semibold">Quick Actions</h2>
                <div class="mt-4 space-y-3">
                    <a href="{{ route('dashboard.events.create') }}" class="w-full inline-flex items-center justify-center rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white hover:bg-gray-800">Create Event</a>
                    <a href="{{ route('dashboard.events.index') }}" class="w-full inline-flex items-center justify-center rounded-md bg-white border px-3 py-2 text-sm font-medium hover:bg-gray-50">Manage Events</a>
                    <a href="{{ route('dashboard.settings.edit') }}" class="w-full inline-flex items-center justify-center rounded-md bg-white border px-3 py-2 text-sm font-medium hover:bg-gray-50">Site Settings</a>
                </div>
            </div>
        </div>
    </div>
@endsection


