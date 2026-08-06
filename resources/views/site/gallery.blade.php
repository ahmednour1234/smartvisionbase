@extends('layouts.site')
@section('title', __('Gallery'))

@section('content')
    <section class="rounded-2xl bg-gradient-to-br from-red-700/60 via-black/70 to-black/80 p-8 md:p-12 text-white">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-semibold leading-tight">{{ __('Past Events Gallery') }}</h1>
            <p class="mt-3 text-white/80 text-lg md:text-xl">{{ __('Highlights from our summits and ceremonies') }}</p>
        </div>
    </section>

    <section class="mt-10 md:mt-14">
        <div class="columns-2 md:columns-3 lg:columns-4 gap-x-4 md:gap-x-6">
            @php
                $imageExts = ['jpg','jpeg','png','webp','gif','bmp'];
            @endphp
            @forelse($files as $file)
                @php
                    $isImage = in_array($file['ext'], $imageExts, true);
                    $name = pathinfo($file['path'], PATHINFO_FILENAME);
                @endphp
                <div class="mb-4 break-inside-avoid">
                    <a href="{{ asset($file['path']) }}" target="_blank" rel="noopener" class="group relative block rounded-xl overflow-hidden bg-white/5 ring-1 ring-white/10 hover:ring-white/20">
                        @if($isImage)
                            <img class="w-full h-auto object-contain transition-transform duration-300 group-hover:scale-[1.02]" src="{{ asset($file['path']) }}" alt="{{ $name }}" loading="lazy" decoding="async">
                        @else
                            <div class="p-6 text-white/80">
                                {{ $name }}.{{ $file['ext'] }}
                            </div>
                        @endif
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent px-3 py-2">
                            <div class="text-xs md:text-sm text-white/90 truncate">{{ $name }}</div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="text-white/70">{{ __('No media uploaded yet.') }}</div>
            @endforelse
        </div>
    </section>
@endsection

