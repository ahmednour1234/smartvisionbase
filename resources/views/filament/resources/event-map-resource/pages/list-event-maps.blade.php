<x-filament-panels::page>
    <div class="space-y-4">

        @php
            $selectedEvent = $this->selectedEvent;
        @endphp

        <div class="rounded-xl border bg-white shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b gap-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Event Booth Map</h2>
                    <p class="text-sm text-gray-500">
                        Showing 100 booths for selected event
                    </p>
                </div>

                <div class="w-72">
                    <select
                        wire:model.live="selectedEventId"
                        class="w-full rounded-lg border-gray-300 text-sm"
                    >
                        <option value="">Select Event</option>

                        @foreach($this->events as $event)
                            <option value="{{ $event->id }}">
                                {{ $event->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if($selectedEvent)
                <div class="px-4 py-4 border-b bg-gray-50">
                    <div class="flex items-center justify-between gap-4 mb-3">
                        <div>
                            <h3 class="text-base font-bold text-gray-900">
                                {{ $selectedEvent->name }}
                            </h3>

                            <p class="text-sm text-gray-500">
                                {{ optional($selectedEvent->start_date)->format('m/d/Y') }}
                                -
                                {{ optional($selectedEvent->end_date)->format('m/d/Y') }}
                            </p>
                        </div>
                    </div>

                  @if($selectedEvent->floor_plan)
    <img
        src="{{ asset('storage/' . $selectedEvent->floor_plan) }}"
        alt="Floor Plan"
        class="w-full max-h-[600px] object-contain rounded-lg"
    >
@else
    <div>No floor plan uploaded for this event.</div>
@endif
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold">Booth</th>
                            <th class="px-4 py-3 text-left font-semibold">Company Name</th>
                            <th class="px-4 py-3 text-left font-semibold">Category</th>
                            <th class="px-4 py-3 text-left font-semibold">Booking Status</th>
                            <th class="px-4 py-3 text-left font-semibold">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($booths as $row)
                            @php
                                $map = $row['map'];
                                $booth = $row['booth'];
                            @endphp

                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $booth }}</td>

                                <td class="px-4 py-3">
                                    {{ $map?->company?->company_name ?? '-' }}
                                </td>

                             

                                <td class="px-4 py-3">
                                @if($map?->category?->name)
    {{ $map->category->name }}
@else
                                        <span class="text-primary-600">
                                            View Sponsorship Package
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @if($map)
                                        <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold
                                            @class([
                                                'bg-green-100 text-green-700' => $map->booking_status === 'Booked',
                                                'bg-blue-100 text-blue-700' => $map->booking_status === 'Confirmed',
                                                'bg-yellow-100 text-yellow-700' => $map->booking_status === 'Complimentary',
                                            ])
                                        ">
                                            {{ $map->booking_status }}
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-lg px-3 py-1 text-xs font-semibold bg-gray-100 text-gray-600">
                                            Empty
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @if($map)
                                        <a
                                            href="{{ \App\Filament\Resources\EventMapResource::getUrl('edit', ['record' => $map]) }}"
                                            class="text-primary-600 font-medium"
                                        >
                                            Edit
                                        </a>
                                    @else
                                        @if($selectedEventId)
                                            <a
                                                href="{{ \App\Filament\Resources\EventMapResource::getUrl('create', [
                                                    'event_id' => $selectedEventId,
                                                    'booth' => $booth,
                                                ]) }}"
                                                class="text-green-600 font-medium"
                                            >
                                                Add Company
                                            </a>
                                        @else
                                            <span class="text-gray-400">
                                                Select event first
                                            </span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t text-sm text-gray-500">
                Showing 100 booths
            </div>
        </div>
    </div>
</x-filament-panels::page>