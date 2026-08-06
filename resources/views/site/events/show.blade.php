@extends('layouts.site')
@section('title', $event->name)

@section('content')
    <article class="mx-auto max-w-5xl">
        <header class="mb-6 text-white">
            <h1 class="text-3xl md:text-4xl font-semibold">{{ $event->name }}</h1>
            <div class="mt-2 text-sm text-white/80">
                @if($event->start_at)
                    {{ $event->start_at->format('F d, Y') }}
                    @if($event->end_at) - {{ $event->end_at->format('F d, Y') }} @endif
                @endif
                <span class="ml-3 inline-flex items-center rounded-full bg-white/10 px-2 py-0.5 text-xs text-white/80 capitalize">{{ $event->status }}</span>
            </div>
        </header>
        <div class="grid md:grid-cols-2 gap-6 items-start">
            <div>
                @if($event->row_image ?? false)
                    <img src="{{ asset($event->row_image) }}" alt="{{ $event->name }}" class="rounded-xl w-full object-cover">
                @elseif($event->cover_image ?? false)
                    <img src="{{ asset($event->cover_image) }}" alt="{{ $event->name }}" class="rounded-xl w-full object-cover">
                @elseif($event->image)
                    <img src="{{ asset($event->image) }}" alt="{{ $event->name }}" class="rounded-xl w-full object-cover">
                @endif
            </div>
            <div class="prose max-w-none text-white/90">
                @if($event->website_url)
                    <p>
                        <a class="text-red-500 hover:underline" href="{{ $event->website_url }}" target="_blank" rel="noopener">
                            {{ __('Visit official website') }}
                        </a>
                    </p>
                @endif
                <p class="">{{ __('Details to be added soon.') }}</p>
            </div>
        </div>
    </article>
@endsection


