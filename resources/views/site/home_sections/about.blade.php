@if($about)
<section class="space-y-8 text-center animate-in" data-direction="right">
    <div>
        <div class="flex items-center gap-4 mb-4">
            <span class="h-px flex-1 bg-white/20"></span>
            <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">{{ __('About SVS') }}</h2>
            <span class="h-px flex-1 bg-white/20"></span>
        </div>
        <p class="mt-4 text-white/80 text-lg md:text-2xl leading-relaxed text-left" dir="rtl">
            {{ \Illuminate\Support\Str::limit(strip_tags($about->content), 50000) }}
        </p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 text-center">
        <div class="text-white rounded-2xl bg-white/[0.06] backdrop-blur p-6 ring-1 ring-white/10 hover:ring-white/20 transition">
            <div class="text-5xl md:text-6xl font-extrabold leading-none text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-white drop-shadow">
                <span class="counter" data-target="5000">0</span><span class="text-red-500">+</span>
            </div>
            <div class="mt-2 text-white/70 text-sm md:text-base">{{ __('Attendance') }}</div>
        </div>
        <div class="text-white rounded-2xl bg-white/[0.06] backdrop-blur p-6 ring-1 ring-white/10 hover:ring-white/20 transition">
            <div class="text-5xl md:text-6xl font-extrabold leading-none text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-white drop-shadow">
                <span class="counter" data-target="200">0</span><span class="text-red-500">+</span>
            </div>
            <div class="mt-2 text-white/70 text-sm md:text-base">{{ __('Speakers') }}</div>
        </div>
        <div class="text-white rounded-2xl bg-white/[0.06] backdrop-blur p-6 ring-1 ring-white/10 hover:ring-white/20 transition">
            <div class="text-5xl md:text-6xl font-extrabold leading-none text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-white drop-shadow">
                <span class="counter" data-target="80">0</span>
            </div>
            <div class="mt-2 text-white/70 text-sm md:text-base">{{ __('Sponsors') }}</div>
        </div>
    </div>
</section>
@endif


