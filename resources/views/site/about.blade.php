@extends('layouts.site')
@section('title', __('About'))

@section('content')
    <section class="rounded-2xl bg-gradient-to-br from-red-700/60 via-black/70 to-black/80 p-8 md:p-12 text-white">
        <div class="text-center max-w-4xl mx-auto">
            <h1 class="text-4xl md:text-6xl font-semibold leading-tight">{{ $about->title }}</h1>
            <p class="mt-3 text-white/80 text-lg md:text-xl">{{ __('Learn more about Smart Vision Summit’s mission and story') }}</p>
        </div>
    </section>

    <section class="mt-10 md:mt-14">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
            @if($about->image)
                <div>
                    <div class="rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/5">
                        <img src="{{ asset($about->image) }}" alt="{{ $about->title }}" class="w-full h-auto object-cover">
                    </div>
                </div>
            @endif
            <div>
                <div class="rounded-2xl ring-1 ring-white/10 bg-black/50 p-6 md:p-8">
                    <div class="text-white/90 leading-relaxed space-y-4">
                        {!! $about->content !!}
                    </div>
                </div>
                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-xl bg-red-600 text-white px-5 py-3 text-base font-semibold hover:bg-red-500 transition">{{ __('Explore Events') }}</a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center rounded-xl bg-white/10 text-white px-5 py-3 text-base font-semibold hover:bg-white/15 ring-1 ring-white/20 transition">{{ __('Contact Us') }}</a>
                </div>
            </div>
        </div>
    </section>
@endsection


