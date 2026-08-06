@extends('layouts.site')
@section('title', __('Sponsors'))

@section('content')
    <div class="space-y-10">
        <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-700/80 via-black/80 to-black/80 text-white p-8 md:p-16">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-semibold">{{ __('Sponsors') }}</h1>
                <p class="mt-3 text-white/80 text-base md:text-lg">{{ __('Our Previous Clients') }}</p>
            </div>
        </section>

        <section>
            <style>
                .s-card {
                    position: relative;
                    overflow: hidden;
                }
                .s-card::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background: linear-gradient(145deg, rgba(220, 38, 38, 0.18), rgba(255, 255, 255, 0.02) 45%, rgba(0, 0, 0, 0.18));
                    pointer-events: none;
                }
                .s-logo-wrap {
                    min-height: 7rem;
                }
                @media (min-width: 768px) {
                    .s-logo-wrap {
                        min-height: 8rem;
                    }
                }
            </style>
            <p class="mb-8 text-center text-sm md:text-base font-medium tracking-[0.2em] uppercase text-white/60">
                {{ __('Our Previous Clients') }}
            </p>
            @foreach($groups as $title => $items)
                <div class="mb-6 text-center">
                    <span class="inline-flex items-center rounded-full bg-gradient-to-r from-red-600/70 via-black/70 to-red-700/70 px-4 py-1.5 text-base md:text-lg font-semibold text-white ring-1 ring-white/10 shadow">
                        {{ $title }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8 mb-12">
                    @foreach($items as $sp)
                        @php
                            $hasLink = filled($sp->url);
                            $tag = $hasLink ? 'a' : 'div';
                        @endphp
                        <{{ $tag }}
                            @if($hasLink) href="{{ $sp->url }}" target="_blank" rel="noopener" @endif
                            class="group s-card rounded-[28px] border border-white/10 bg-white/[0.04] p-5 md:p-6 shadow-[0_18px_50px_rgba(0,0,0,0.28)] transition duration-300 hover:-translate-y-1 hover:border-red-500/30 hover:shadow-[0_24px_60px_rgba(0,0,0,0.35)] {{ $hasLink ? 'focus:outline-none focus:ring-2 focus:ring-red-600/40' : '' }}"
                        >
                            <div class="relative z-10 flex h-full flex-col gap-4">
                                <div class="inline-flex w-fit items-center rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-red-200">
                                    Sponsor
                                </div>
                                <div class="s-logo-wrap grid place-items-center rounded-[22px] bg-white px-5 py-5 shadow-[0_12px_30px_rgba(15,23,42,0.12)] ring-1 ring-slate-200/80">
                                    @if($sp->logo)
                                        <img src="{{ asset($sp->logo) }}" alt="{{ $sp->name }}" class="max-h-20 md:max-h-24 w-auto max-w-full object-contain transition-transform duration-300 group-hover:scale-105" loading="lazy" decoding="async"/>
                                    @else
                                        <span class="text-slate-900 text-lg font-semibold text-center">{{ $sp->name }}</span>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <h3 class="text-lg md:text-xl font-semibold text-white">{{ $sp->name }}</h3>
                                    <p class="text-sm text-white/55">{{ $title }}</p>
                                </div>
                            </div>
                        </{{ $tag }}>
                    @endforeach
                </div>
            @endforeach
        </section>
    </div>
@endsection



