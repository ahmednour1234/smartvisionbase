@if(isset($faqs) && $faqs->count())
<section class="animate-in" data-direction="right">
    <div class="flex items-center gap-4 mb-6">
        <span class="h-px flex-1 bg-white/20"></span>
        <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">{{ __('FAQ') }}</h2>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>
    <div class="space-y-3">
        @foreach($faqs as $faq)
            <details class="group rounded-2xl ring-1 ring-white/10 bg-white/5 p-4 md:p-5 text-white/90 overflow-hidden">
                <summary class="cursor-pointer text-sm md:text-base font-semibold flex items-center gap-3">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-red-600 text-white text-xs ring-1 ring-white/10">?</span>
                    <span>{{ $faq->question }}</span>
                    <span class="ml-auto h-5 w-5 rounded-full bg-white/10 ring-1 ring-white/10 inline-flex items-center justify-center transition group-open:rotate-45">+</span>
                </summary>
                <div class="mt-3 text-white/70 text-sm md:text-base leading-relaxed">{!! nl2br(e($faq->answer)) !!}</div>
            </details>
        @endforeach
    </div>
</section>
@endif


