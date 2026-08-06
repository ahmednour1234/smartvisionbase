<section class="rounded-2xl bg-gradient-to-r from-red-700/70 via-black/70 to-black/80 ring-1 ring-white/10 p-6 md:p-10 text-white animate-in" data-direction="left">
    <div class="flex flex-col md:flex-row items-center gap-4 md:gap-6">
        <div class="text-center md:text-left">
            <h3 class="text-2xl md:text-3xl font-semibold">{{ __('Ready to join Smart Vision Summit?') }}</h3>
            <p class="mt-1 text-white/80 text-sm md:text-base">{{ __('Become a sponsor or register for upcoming events today.') }}</p>
        </div>
        <div class="mt-3 md:mt-0 md:ml-auto flex items-center gap-3">
            <a href="{{ route('contact') }}" class="inline-flex items-center rounded-xl bg-white text-black px-4 md:px-5 py-2 md:py-2.5 text-sm md:text-base font-semibold hover:bg-zinc-100 transition">{{ __('Become Sponsor') }}</a>
            <a href="{{ route('events.index') }}" class="inline-flex items-center rounded-xl bg-red-600 text-white px-4 md:px-5 py-2 md:py-2.5 text-sm md:text-base font-semibold hover:bg-red-500 transition">{{ __('Register Now') }}</a>
        </div>
    </div>
</section>


