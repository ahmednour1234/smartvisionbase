@extends('layouts.site')
@section('title', __('Contact'))

@section('content')
    @php
        $address = \App\Models\Setting::getValue(app()->getLocale() === 'ar' ? 'address_ar' : 'address_en');
        $phone = \App\Models\Setting::getValue('phone');
        $email = \App\Models\Setting::getValue('email');
        $mapLat = \App\Models\Setting::getValue('map_lat');
        $mapLng = \App\Models\Setting::getValue('map_lng');
        $facebook = \App\Models\Setting::getValue('facebook');
        $instagram = \App\Models\Setting::getValue('instagram');
        $linkedin = \App\Models\Setting::getValue('linkedin');
        $youtube = \App\Models\Setting::getValue('youtube');
        $x = \App\Models\Setting::getValue('x');
        $whatsapp = \App\Models\Setting::getValue('whatsapp');
    @endphp

    <section class="rounded-2xl bg-gradient-to-r from-black via-black to-red-700 p-8 md:p-12 text-white mb-10">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl md:text-5xl font-semibold">{{ __('Contact Us') }}</h1>
            <p class="mt-3 text-white/80">{{ __('We’d love to hear from you. Send us a message and our team will get back soon.') }}</p>
        </div>
    </section>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
        <div class="rounded-2xl border border-white/10 bg-black/60 p-6 md:p-8">
            <form method="POST" action="{{ route('contact.send') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/80">{{ __('Name') }}</label>
                    <input name="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-white/20 bg-white/5 text-white placeholder-white/50"/>
                    @error('name') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-white/80">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-white/20 bg-white/5 text-white placeholder-white/50"/>
                        @error('email') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/80">{{ __('Phone') }}</label>
                        <input name="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-white/20 bg-white/5 text-white placeholder-white/50"/>
                        @error('phone') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80">{{ __('Subject') }}</label>
                    <input name="subject" value="{{ old('subject') }}" class="mt-1 block w-full rounded-md border-white/20 bg-white/5 text-white placeholder-white/50"/>
                    @error('subject') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80">{{ __('Message') }}</label>
                    <textarea name="message" rows="6" class="mt-1 block w-full rounded-md border-white/20 bg-white/5 text-white placeholder-white/50">{{ old('message') }}</textarea>
                    @error('message') <p class="text-sm text-red-300 mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="pt-2">
                    <button class="inline-flex items-center rounded-xl bg-red-600 px-6 py-3 text-base font-semibold text-white hover:bg-red-500">
                        {{ __('Send Message') }}
                    </button>
                </div>
            </form>
        </div>
        <div class="space-y-6">
            <div class="rounded-2xl ring-1 ring-white/10 bg-white/5 p-6">
                <div class="space-y-3 text-white/90">
                    @if($address)
                        <p class="flex items-start gap-3"><svg viewBox="0 0 24 24" class="h-6 w-6 text-white/70 fill-current mt-0.5"><path d="M12 2a7 7 0 0 0-7 7c0 5.25 7 13 7 13s7-7.75 7-13a7 7 0 0 0-7-7zm0 9.5a2.5 2.5 0 1 1 0-5 2.5 2.5 0 0 1 0 5z"/></svg><span>{{ $address }}</span></p>
                    @endif
                    @if($phone)
                        <p class="flex items-center gap-3"><svg viewBox="0 0 24 24" class="h-6 w-6 text-white/70 fill-current"><path d="M6.62 10.79a15.54 15.54 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.02-.24c1.12.37 2.33.57 3.57.57a1 1 0 0 1 1 1V21a1 1 0 0 1-1 1C10.85 22 2 13.15 2 2a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.24.2 2.45.57 3.57a1 1 0 0 1-.24 1.02l-2.2 2.2z"/></svg><span>{{ $phone }}</span></p>
                    @endif
                    @if($email)
                        <p class="flex items-center gap-3"><svg viewBox="0 0 24 24" class="h-6 w-6 text-white/70 fill-current"><path d="M20 4H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/></svg><span>{{ $email }}</span></p>
                    @endif
                </div>
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @if($facebook)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $facebook }}" target="_blank" rel="noopener" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M22 12.06C22 6.48 17.52 2 11.94 2S2 6.48 2 12.06C2 17.08 5.66 21.2 10.44 22v-7.03H7.9v-2.91h2.54V9.41c0-2.5 1.49-3.88 3.77-3.88 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.62.77-1.62 1.56v1.87h2.76l-.44 2.91h-2.32V22C18.34 21.2 22 17.08 22 12.06z"/></svg>
                        </a>
                    @endif
                    @if($linkedin)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $linkedin }}" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M19 3A2.94 2.94 0 0 1 22 6v12a2.94 2.94 0 0 1-3 3H5a2.94 2.94 0 0 1-3-3V6a2.94 2.94 0 0 1 3-3h14zM8.34 18.34V9.69H5.69v8.65h2.65zM7 8.46A1.53 1.53 0 1 0 7 5.4a1.53 1.53 0 0 0 0 3.06zM18.34 18.34v-4.69c0-2.51-1.34-3.68-3.12-3.68a2.7 2.7 0 0 0-2.44 1.34h-.06V9.69H10c.03.74 0 8.65 0 8.65h2.69v-4.84c0-.26.02-.52.1-.71.22-.52.73-1.06 1.59-1.06 1.12 0 1.57.8 1.57 1.97v4.64h2.39z"/></svg>
                        </a>
                    @endif
                    @if($youtube)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $youtube }}" target="_blank" rel="noopener" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19 3.5 12 3.5 12 3.5s-7 0-9.4.6A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1C5 20.5 12 20.5 12 20.5s7 0 9.4-.6a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8zM9.75 15.5v-7l6.25 3.5-6.25 3.5z"/></svg>
                        </a>
                    @endif
                    @if($instagram)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $instagram }}" target="_blank" rel="noopener" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M7 2C4.24 2 2 4.24 2 7v10c0 2.76 2.24 5 5 5h10c2.76 0 5-2.24 5-5V7c0-2.76-2.24-5-5-5H7zm10 2c1.66 0 3 1.34 3 3v10c0 1.66-1.34 3-3 3H7c-1.66 0-3-1.34-3-3V7c0-1.66 1.34-3 3-3h10zm-5 3.5A5.5 5.5 0 1 0 17.5 13 5.51 5.51 0 0 0 12 7.5zm0 2A3.5 3.5 0 1 1 8.5 13 3.5 3.5 0 0 1 12 9.5zM18 6.2a.8.8 0 1 0 .8.8.8.8 0 0 0-.8-.8z"/></svg>
                        </a>
                    @endif
                    @if($x)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $x }}" target="_blank" rel="noopener" aria-label="X">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M18.244 2H21l-6.51 7.44L22 22h-6.828l-4.88-6.36L4.6 22H2l6.98-7.98L2 2h6.828l4.486 5.85L18.244 2zm-1.197 18h1.686L7.03 4H5.343l11.704 16z"/></svg>
                        </a>
                    @endif
                    @if($whatsapp)
                        <a class="inline-flex items-center justify-center w-10 h-10 rounded-md bg-white/10 ring-1 ring-white/10 hover:bg-white/15 transition" href="{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="WhatsApp">
                            <svg viewBox="0 0 24 24" class="h-5 w-5 fill-current"><path d="M20.52 3.48A11.94 11.94 0 0 0 12.01 0C5.38 0 .02 5.36.02 11.99c0 2.11.55 4.17 1.59 5.99L0 24l6.2-1.61a12 12 0 0 0 5.81 1.48h.01c6.63 0 11.99-5.36 11.99-11.99a11.9 11.9 0 0 0-3.49-8.4zM12 21.8h-.01a9.8 9.8 0 0 1-4.99-1.37l-.36-.21-3.68.96.98-3.58-.24-.37A9.81 9.81 0 1 1 21.8 12c0 5.41-4.39 9.8-9.8 9.8zm5.52-7.35c-.3-.15-1.78-.88-2.05-.97-.27-.1-.47-.15-.66.15-.2.3-.77.97-.95 1.16-.18.2-.35.22-.65.08-.3-.15-1.27-.47-2.42-1.5-.9-.8-1.51-1.78-1.69-2.08-.18-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.38-.02-.53-.07-.15-.66-1.6-.9-2.18-.24-.58-.48-.5-.66-.5-.17 0-.38-.02-.58-.02-.2 0-.53.08-.81.38-.27.3-1.06 1.03-1.06 2.5s1.09 2.9 1.25 3.1c.15.2 2.14 3.26 5.18 4.57.72.31 1.28.49 1.72.63.72.23 1.38.2 1.9.12.58-.09 1.78-.73 2.03-1.43.25-.7.25-1.3.17-1.43-.07-.13-.27-.2-.57-.35z"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            <div class="rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/5">
                @if($mapLat && $mapLng)
                    <iframe
                        src="https://www.google.com/maps?q={{ $mapLat }},{{ $mapLng }}&z=14&output=embed"
                        width="100%" height="280" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade" class="w-full h-64 md:h-72"></iframe>
                @else
                    <div class="p-6 text-white/70 text-center">{{ __('Map coordinates are not set.') }}</div>
                @endif
            </div>
        </div>
    </div>
@endsection


