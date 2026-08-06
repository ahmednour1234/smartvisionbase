<section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-700/80 via-black/80 to-black/80 text-white p-6 md:p-16 animate-in" data-direction="up">
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-red-600/30 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[.04]" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.9) 1px, transparent 1px); background-size: 6px 6px;"></div>
    </div>
    <div class="flex flex-col items-center text-center gap-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-semibold leading-tight">Smart Vision Summit</h1>
            <p class="mt-4 text-white/90 text-lg md:text-2xl">Connecting Financial Markets Across Borders</p>
            <div class="mx-auto h-0.5 w-16 bg-red-500/60 rounded-full mt-3"></div>
        </div>
        <div class="w-full max-w-5xl">
            <div class="relative bg-gradient-to-r from-red-700 to-red-500 p-[6px] rounded-2xl shadow-2xl">
                <div class="relative rounded-2xl overflow-hidden min-h-[20rem] md:min-h-[28rem]"
                     style="background-image: url('{{ $nearest && ($nearest->cover_image ?? $nearest->image) ? asset($nearest->cover_image ?? $nearest->image) : '' }}'); background-size: cover; background-position: center;">
                    <div class="absolute inset-0 bg-black/60"></div>
                    <div class="relative px-6 py-12 md:px-16 md:py-20 text-white h-full flex items-center">
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs md:text-sm uppercase tracking-wide text-white/90 ring-1 ring-white/20 backdrop-blur">
                                <span class="h-1.5 w-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                Next in line
                            </span>
                        </div>
                        <div class="relative w-full grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="pointer-events-none absolute inset-x-0 top-1/2 h-px bg-white/20 md:hidden"></div>
                            <div class="pointer-events-none absolute inset-y-0 left-1/2 w-px bg-white/20 hidden md:block"></div>

                            @if($nearest && ($nearest->row_image ?? false))
                                <div class="md:pr-8">
                                    <div class="rounded-xl bg-black/40 p-2 shadow-xl">
                                        <img src="{{ asset($nearest->row_image) }}" alt="{{ $nearest->name }}" class="w-full h-auto max-h-72 object-contain">
                                    </div>
                                </div>
                            @endif
                            <div class="text-center md:text-left md:pl-8">
                                @if($nearest)
                                    <div class="mt-3 text-2xl md:text-5xl font-semibold">{{ $nearest->name }}</div>
                                    <div class="mt-4 inline-flex items-center gap-3 text-white/90 text-xl md:text-4xl font-semibold tracking-wide whitespace-nowrap">
                                        @if($nearest->start_at)
                                            <span>{{ $nearest->start_at->format('F d') }}</span>
                                            @if($nearest->end_at)
                                                <span class="h-5 w-px bg-white/60"></span>
                                                <span>{{ $nearest->end_at->format('d, Y') }}</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="mt-10 flex items-center justify-center md:justify-start gap-3 md:gap-4 w-full">
                                        <a class="inline-flex items-center justify-center w-full md:w-auto rounded-2xl bg-red-600 text-white px-5 md:px-8 py-3 md:py-4 text-base md:text-xl font-semibold shadow-lg hover:bg-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/40 transition will-change-transform hover:-translate-y-0.5"
                                           href="{{ $nearest->website_url ?? route('events.show', $nearest->slug) }}">
                                            Join Us
                                        </a>
                                        <a class="inline-flex items-center justify-center w-full md:w-auto rounded-2xl bg-white/10 text-white px-5 md:px-8 py-3 md:py-4 text-base md:text-xl font-semibold hover:bg-white/15 ring-1 ring-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/40 transition"
                                           href="{{ route('about') }}">
                                            Learn More
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-3 text-white/80">No upcoming event yet.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-6 flex justify-center">
        <span class="inline-flex items-center gap-2 text-white/70 text-sm">
            <span class="h-1 w-1 rounded-full bg-white/60 animate-bounce"></span>
            Scroll
        </span>
    </div>
</section>


