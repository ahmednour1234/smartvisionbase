<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'SmartVision'))</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { font-size: 17px; line-height: 1.6; }
        /* Global input styles */
        input[type="text"], input[type="email"], input[type="tel"], input[type="url"],
        input[type="datetime-local"], input[type="password"], textarea, select {
            border: 1px solid rgba(255,255,255,.25);
            border-radius: .75rem;
            padding: .75rem 1rem;
            background: rgba(255,255,255,.08);
            color: #fff;
            transition: border-color .15s ease, box-shadow .15s ease, background-color .15s ease;
        }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,.65); }
        input:focus, textarea:focus, select:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(239,68,68,.25);
            border-color: rgba(239,68,68,.8);
            background: rgba(255,255,255,.1);
        }
        .btn-primary { background: linear-gradient(90deg, #ef4444, #0a0a0a); color: #fff; }
        .btn-primary:hover { filter: brightness(1.05); }
        .btn-outline { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.25); color: #fff; }
        .btn-outline:hover { background: rgba(255,255,255,.1); }
        .btn-cta {
            display: inline-flex; align-items: center; justify-content: center;
            padding: .9rem 1.5rem; border-radius: 1rem; font-weight: 600;
            background: linear-gradient(90deg, #ffffff, #efefef); color: #0a0a0a;
            box-shadow: 0 10px 25px rgba(239,68,68,.25);
        }
        @media (min-width: 768px) {
            .btn-cta { padding: 1.1rem 2rem; font-size: 1.125rem; }
        }
    </style>
</head>
@php
    $navLink = function (string $label, string $routeName, array $params = []) {
        $active = request()->routeIs($routeName);
        $classes = $active ? 'text-white' : 'text-white/80 hover:text-white';
        return '<a href="'.e(route($routeName, $params)).'" class="text-base md:text-lg '.$classes.'">'.$label.'</a>';
    };
@endphp
<body class="site-shell min-h-screen text-slate-100">
<style>
    .site-shell {
        background-color: #000000;
        background-image:
            radial-gradient(circle at top, rgba(220, 38, 38, 0.26), transparent 30%),
            linear-gradient(180deg, rgba(0, 0, 0, 0.86), rgba(0, 0, 0, 0.94)),
            url('https://smartvisionsummit.com/wp-content/uploads/2025/03/motion-flow1920.webp');
        background-position: top center, center, top center;
        background-repeat: no-repeat, no-repeat, repeat-y;
        background-size: auto, cover, min(100%, 1440px);
        background-attachment: scroll, scroll, scroll;
    }
    main {
        min-height: 60vh;
    }
    @media (max-width: 768px) {
        .site-shell {
            background-size: auto, cover, 180%;
        }
    }
</style>

<header class="border-b bg-gradient-to-r from-black via-black to-red-700 text-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            @php
                $logo = \App\Models\Setting::getValue('logo_upload') ?: \App\Models\Setting::getValue('logo');
                $facebook = \App\Models\Setting::getValue('facebook');
                $instagram = \App\Models\Setting::getValue('instagram');
                $linkedin = \App\Models\Setting::getValue('linkedin');
                $youtube = \App\Models\Setting::getValue('youtube');
                $x = \App\Models\Setting::getValue('x');
                $logoUrl = $logo ? (\Illuminate\Support\Str::startsWith($logo, ['http://','https://','//']) ? $logo : asset($logo)) : null;
                $nearestEvent = \App\Models\Event::query()->nearest();
            @endphp

            {{-- مجموعة الشمال: أيقونة الموبايل + اللوجو مع مسافة بينهم --}}
    <div class="flex items-center" style="gap:15rem;">
    <a href="{{ route('home') }}" class="text-xl md:text-2xl font-semibold flex items-center gap-3">
        @if($logoUrl)
                <img src="{{ $logoUrl }}" alt="Smart Vision Summit" class="h-10 md:h-10 w-auto object-contain">
            <span class="sr-only">Smart Vision Summit</span>
        @else
            <span class="bg-clip-text text-transparent bg-gradient-to-r from-white to-red-300">SmartVision</span>
        @endif
    </a>

    <button id="mobile-nav-toggle"
            class="md:hidden inline-flex items-center justify-center rounded-md ring-1 ring-white/10 bg-white/5 text-white px-3 py-2">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M4 6h16v2H4V6zm0 5h16v2H4v-2zm0 5h16v2H4v-2z"/>
        </svg>
    </button>
</div>


            {{-- النافبار الديسكتوب --}}
            <nav class="hidden md:flex items-center gap-6">
                {!! $navLink(__('Home'), 'home') !!}
                {!! $navLink(__('Events'), 'events.index') !!}
                {!! $navLink(__('About'), 'about') !!}
                {!! $navLink(__('Gallery'), 'gallery') !!}
                {!! $navLink(__('Contact'), 'contact') !!}
            </nav>

            {{-- CTA اليمين --}}
            <div class="flex items-center gap-3">
                @if($nearestEvent)
                    <a href="{{ $nearestEvent->website_url ?? route('events.show', $nearestEvent->slug) }}"
                       class="hidden md:inline-flex items-center rounded-xl bg-red-600 text-white px-3 py-2 text-sm font-semibold hover:bg-red-500 transition">
                        {{ __('Next Event') }}
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div id="mobile-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-40"></div>

    <div id="mobile-drawer"
         class="fixed inset-y-0 right-0 w-72 max-w-[80%] bg-black/95 ring-1 ring-white/10 shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="h-full flex flex-col">
            <div class="px-4 py-4 border-b border-white/10 flex items-center justify-between">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo" class="h-8 w-auto object-contain opacity-90">
                    @else
                        <span class="text-white font-semibold">SmartVision</span>
                    @endif
                </a>
                <button id="mobile-nav-close"
                        class="inline-flex items-center justify-center rounded-md ring-1 ring-white/10 bg-white/5 text-white p-2">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M6.225 4.811L4.811 6.225 10.586 12l-5.775 5.775 1.414 1.414L12 13.414l5.775 5.775 1.414-1.414L13.414 12l5.775-5.775-1.414-1.414L12 10.586z"/>
                    </svg>
                </button>
            </div>
            <div class="px-4 py-4 flex-1 overflow-y-auto text-white/90 space-y-3">
                <a href="{{ route('home') }}" class="block hover:text-white">{{ __('Home') }}</a>
                <a href="{{ route('events.index') }}" class="block hover:text-white">{{ __('Events') }}</a>
                <a href="{{ route('about') }}" class="block hover:text-white">{{ __('About') }}</a>
                <a href="{{ route('gallery') }}" class="block hover:text-white">{{ __('Gallery') }}</a>
                <a href="{{ route('contact') }}" class="block hover:text-white">{{ __('Contact') }}</a>
            </div>
            @if($nearestEvent)
                <div class="px-4 py-4 border-t border-white/10">
                    <a href="{{ $nearestEvent->website_url ?? route('events.show', $nearestEvent->slug) }}"
                       class="inline-flex w-full items-center justify-center rounded-xl bg-red-600 text-white px-4 py-2.5 text-sm font-semibold hover:bg-red-500 transition">
                        {{ __('Next Event') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</header>

<main class="py-12 md:py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <x-alert />
        @yield('content')
    </div>
</main>

<footer class="border-t bg-gradient-to-r from-black via-black to-red-700 text-white">
    @php
        $address = \App\Models\Setting::getValue(app()->getLocale() === 'ar' ? 'address_ar' : 'address_en');
        $phone = \App\Models\Setting::getValue('phone');
        $email = \App\Models\Setting::getValue('email');
        $facebook = \App\Models\Setting::getValue('facebook');
        $instagram = \App\Models\Setting::getValue('instagram');
        $linkedin = \App\Models\Setting::getValue('linkedin');
        $youtube = \App\Models\Setting::getValue('youtube');
        $x = \App\Models\Setting::getValue('x');
        $footerLogo = \App\Models\Setting::getValue('logo_upload') ?: \App\Models\Setting::getValue('logo');
        $footerLogoUrl = $footerLogo ? (\Illuminate\Support\Str::startsWith($footerLogo, ['http://','https://','//']) ? $footerLogo : asset($footerLogo)) : null;
        $whatsapp = \App\Models\Setting::getValue('whatsapp');
        $mapLat = \App\Models\Setting::getValue('map_lat');
        $mapLng = \App\Models\Setting::getValue('map_lng');
    @endphp
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
        <div class="rounded-2xl ring-1 ring-white/10 bg-black/30 p-6 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-12 items-start">
                <div>
                    <h5 class="text-3xl md:text-5xl font-semibold">{{ __('Contact Us') }}</h5>
                    @if($address || $phone)
                        <p class="mt-4 text-white text-lg md:text-xl leading-relaxed">
                            @if($address){{ $address }}@endif
                            @if($address && $phone) <span class="mx-2">•</span> @endif
                            @if($phone){{ $phone }}@endif
                        </p>
                    @endif
                    <div class="mt-6">
                        <a class="inline-block mb-4" href="{{ route('home') }}">
                            @if($footerLogoUrl)
                                <img class="h-14 md:h-16 w-auto object-contain" src="{{ $footerLogoUrl }}" alt="Site Logo">
                            @else
                                <span class="text-xl md:text-2xl font-semibold">Smart Vision Summit</span>
                            @endif
                        </a>
                        <ul class="mt-2 flex flex-wrap items-center gap-2">
                            @if($facebook)
                                <li>
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition"
                                       href="{{ $facebook }}" target="_blank" rel="noopener" aria-label="Facebook">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path
                                                d="M22 12.06C22 6.48 17.52 2 11.94 2S2 6.48 2 12.06C2 17.08 5.66 21.2 10.44 22v-7.03H7.9v-2.91h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.62.77-1.62 1.56v1.87h2.76l-.44 2.91h-2.32V22C18.34 21.2 22 17.08 22 12.06z"/>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if($linkedin)
                                <li>
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition"
                                       href="{{ $linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path
                                                d="M19 3A2.94 2.94 0 0 1 22 6v12a2.94 2.94 0 0 1-3 3H5a2.94 2.94 0 0 1-3-3V7a2.94 2.94 0 0 1 3-3h14zM8.34 18.34V9.69H5.69v8.65h2.65zM7 8.46A1.53 1.53 0 1 0 7 5.4a1.53 1.53 0 0 0 0 3.06zM18.34 18.34v-4.69c0-2.51-1.34-3.68-3.12-3.68a2.7 2.7 0 0 0-2.44 1.34h-.06V9.69H10c.03.74 0 8.65 0 8.65h2.69v-4.84c0-.26.02-.52.1-.71.22-.52.73-1.06 1.59-1.06 1.12 0 1.57.8 1.57 1.97v4.64h2.39z"/>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if($youtube)
                                <li>
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition"
                                       href="{{ $youtube }}" target="_blank" rel="noopener" aria-label="YouTube">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path
                                                d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19 3.5 12 3.5 12 3.5s-7 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1C5 20.5 12 20.5 12 20.5s7 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.75 15.5v-7l6.25 3.5-6.25 3.5z"/>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if($instagram)
                                <li>
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition"
                                       href="{{ $instagram }}" target="_blank" rel="noopener" aria-label="Instagram">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path
                                                d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2c1.66 0 3 1.34 3 3v10c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h10zm-5 3.5A5.5 5.5 0 1 0 17.5 13 5.51 5.51 0 0 0 12 7.5zm0 2A3.5 3.5 0 1 1 8.5 13 3.5 3.5 0 0 1 12 9.5zM18 6.2a.8.8 0 1 0 .8.8.8.8 0 0 0-.8-.8z"/>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                            @if($x)
                                <li>
                                    <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition"
                                       href="{{ $x }}" target="_blank" rel="noopener" aria-label="X">
                                        <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current">
                                            <path
                                                d="M18.244 2H21l-6.51 7.44L22 22h-6.828l-4.88-6.36L4.6 22H2l6.98-7.98L2 2h6.828l4.486 5.85L18.244 2zm-1.197 18h1.686L7.03 4H5.343l11.704 16z"/>
                                        </svg>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                <div>
                    <div class="w-full h-full rounded-xl overflow-hidden ring-1 ring-white/10 bg-white/5">
                        @if($mapLat && $mapLng)
                            <iframe
                                src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&z=14&output=embed"
                                width="100%" height="360" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full h-[320px] md:h-[420px]"></iframe>
                        @else
                            <div class="p-6 text-white/70 text-center">
                                {{ __('Map coordinates are not set.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-4 text-sm text-white/70 flex items-center justify-between">
            <span>&copy; {{ date('Y') }} Smart Vision</span>
            <span></span>
        </div>
    </div>
</footer>

<script>
    (function () {
        const btn = document.getElementById('mobile-nav-toggle');
        const drawer = document.getElementById('mobile-drawer');
        const overlay = document.getElementById('mobile-overlay');
        const closeBtn = document.getElementById('mobile-nav-close');

        const open = () => {
            if (!drawer || !overlay) return;
            overlay.classList.remove('hidden');
            drawer.classList.remove('translate-x-full');
        };
        const close = () => {
            if (!drawer || !overlay) return;
            drawer.classList.add('translate-x-full');
            overlay.classList.add('hidden');
        };

        if (btn && drawer && overlay) {
            btn.addEventListener('click', open);
        }
        if (overlay) overlay.addEventListener('click', close);
        if (closeBtn) closeBtn.addEventListener('click', close);
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') close();
        });
    })();
</script>
</body>
</html>
