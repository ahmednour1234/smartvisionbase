@foreach($items as $sp)
    @php
        $hasLink = filled($sp->url);
        $tag = $hasLink ? 'a' : 'div';
    @endphp

    <{{ $tag }}
        @if($hasLink)
            href="{{ $sp->url }}"
            target="_blank"
            rel="noopener"
        @endif
        class="group s-card rounded-[28px] border border-white/10 bg-white/[0.04] p-5 md:p-6 shadow-[0_18px_50px_rgba(0,0,0,0.28)] transition duration-300 hover:-translate-y-1 hover:border-red-500/30 hover:shadow-[0_24px_60px_rgba(0,0,0,0.35)]"
    >
        <div class="relative z-10 flex h-full flex-col gap-4">
            <div class="inline-flex w-fit items-center rounded-full border border-red-500/20 bg-red-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-red-200">
                Sponsor
            </div>

            <div class="s-logo-wrap grid place-items-center rounded-[22px] bg-white px-5 py-5 shadow-[0_12px_30px_rgba(15,23,42,0.12)] ring-1 ring-slate-200/80">
                @if($sp->logo)
                    <img
                        src="{{ asset($sp->logo) }}"
                        alt="{{ $sp->name }}"
                        class="max-h-20 md:max-h-24 w-auto max-w-full object-contain transition-transform duration-300 group-hover:scale-105"
                        loading="lazy"
                    >
                @else
                    <span class="text-slate-900 text-lg font-semibold text-center">
                        {{ $sp->name }}
                    </span>
                @endif
            </div>

            <div class="space-y-1">
                <h3 class="text-lg md:text-xl font-semibold text-white">
                    {{ $sp->name }}
                </h3>

                <p class="text-sm text-white/55">
                    {{ $title }}
                </p>
            </div>
        </div>
    </{{ $tag }}>
@endforeach