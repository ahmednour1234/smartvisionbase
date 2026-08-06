@extends('layouts.site')
@section('title', __('Events'))

@section('content')
    <div class="flex items-center gap-4 mb-6">
        <span class="h-px flex-1 bg-white/20"></span>
        <h1 class="text-3xl font-semibold text-white text-center">{{ __('Events') }}</h1>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @forelse($events as $event)
            <div class="relative rounded-3xl overflow-hidden bg-black/90 border border-white/10 shadow-2xl ring-1 ring-white/5 transition hover:-translate-y-1 hover:shadow-[0_18px_60px_rgba(0,0,0,0.8)] min-h-[22rem] md:min-h-[24rem] flex flex-col">
                @if($event->cover_image ?? $event->image)
                    <div class="absolute inset-0">
                        <div class="w-full h-full bg-cover bg-center opacity-30"
                             style="background-image: url('{{ asset(($event->cover_image ?? $event->image) ?? '') }}');">
                        </div>
                    </div>
                @endif
                <div class="relative p-6 md:p-8 grow">
                    <div class="text-white">
                        <h3 class="text-2xl md:text-3xl font-semibold">{{ $event->name }}</h3>
                        <div class="mt-1 text-white/80 text-base md:text-lg">
                            @if($event->start_at)
                                {{ $event->start_at->format('F d') }} @if($event->end_at) - {{ $event->end_at->format('d, Y') }} @endif
                            @endif
                        </div>
                    </div>
                    @if(!empty($event->row_image))
                        <div class="mt-4   h-24 md:h-28 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset($event->row_image) }}" alt="{{ $event->name }}" class="max-h-full w-auto object-contain opacity-95">
                        </div>
                    @endif
                </div>
                <div class="relative border-t border-white/10 p-4 flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                    <a href="{{ $event->website_url ?? route('events.show', $event->slug) }}" class="inline-flex items-center justify-center w-full sm:w-auto rounded-xl bg-white/10 text-white px-5 py-3 text-base md:text-lg hover:bg-white/15 ring-1 ring-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 transition">{{ __('Details') }}</a>
                    <a href="{{ $event->website_url ?? route('events.show', $event->slug) }}" class="inline-flex items-center justify-center w-full sm:w-auto rounded-xl bg-red-600 text-white px-5 py-3 text-base md:text-lg hover:bg-red-500 shadow-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/30 transition">
                        {{ __('Join Now') }}
                        <svg class="ml-2 h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.172 12l-4.95-4.95 1.414-1.414L16 12l-6.364 6.364-1.414-1.414z"/></svg>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full text-gray-500">{{ __('No events yet.') }}</div>
        @endforelse
    </div>
    <div class="mt-8">
        {{ $events->links() }}
    </div>
@endsection


