@if(isset($testimonials) && $testimonials->count())
<section class="space-y-8 animate-in" data-direction="right">
    <div class="flex items-center gap-4 mb-2">
        <span class="h-px flex-1 bg-white/20"></span>
        <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">{{ __('What People Say') }}</h2>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>
    <style>
        .tcard { opacity: .5; transform: scale(.96); transition: opacity .5s ease, transform .5s ease; }
        .tcard.is-center { opacity: 1; transform: scale(1); }
    </style>
    <div id="testimonials-slider" class="relative overflow-hidden rounded-2xl ring-1 ring-white/10 bg-white/5">
        <div class="slider-track flex transition-transform duration-500 ease-out" style="will-change: transform;">
            @foreach($testimonials as $idx => $t)
                <div class="shrink-0 w-full md:w-1/3 p-4 md:p-5">
                    <div
                        class="tcard h-full rounded-2xl ring-1 ring-white/10 bg-black/50 p-5 md:p-6 text-white/90 relative flex flex-col items-center text-center cursor-pointer"
                        data-name="{{ $t->name }}"
                        data-role="{{ $t->role }}"
                        data-quote="{{ e($t->quote) }}"
                        data-avatar="{{ !empty($t->avatar) ? asset($t->avatar) : '' }}"
                    >
                        @if(!empty($t->avatar))
                            <div class="relative -mt-10 mb-3">
                                <div class="h-16 w-16 md:h-20 md:w-20 rounded-full ring-2 ring-white/20 bg-white/10 overflow-hidden shadow-lg mx-auto">
                                    <img src="{{ asset($t->avatar) }}" alt="{{ $t->name }}" class="h-full w-full object-cover">
                                </div>
                            </div>
                        @endif
                        <p class="text-sm md:text-base leading-relaxed">“{{ $t->quote }}”</p>
                        <div class="mt-3 text-white/60 text-xs md:text-sm">— {{ $t->name }} @if(!empty($t->role)) · <span class="text-white/50">{{ $t->role }}</span>@endif</div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="absolute inset-x-0 bottom-2 flex items-center justify-center gap-2">
            @for($i=0; $i<$testimonials->count(); $i++)
                <span class="dot h-1.5 w-1.5 rounded-full bg-white/30"></span>
            @endfor
        </div>
    </div>

    <div id="testimonial-modal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70"></div>
        <div class="relative mx-auto w-[92%] max-w-xl mt-24">
            <div class="rounded-2xl bg-gradient-to-br from-red-700/80 via-black/90 to-black/90 ring-1 ring-white/10 p-6 md:p-8 text-white shadow-2xl">
                <button type="button" class="tm-close absolute top-2 right-2 inline-flex h-8 w-8 items-center justify-center rounded-full bg-white/10 hover:bg-white/15 ring-1 ring-white/10">✕</button>
                <div class="flex flex-col items-center text-center">
                    <div class="tm-avatar h-16 w-16 md:h-20 md:w-20 rounded-full ring-2 ring-white/20 bg-white/10 overflow-hidden shadow-lg mb-4"></div>
                    <p class="tm-quote text-base md:text-lg leading-relaxed"></p>
                    <div class="mt-4 tm-name text-white/80 font-semibold"></div>
                    <div class="tm-role text-white/60 text-sm"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('testimonial-modal');
            if (!modal) return;
            const quoteEl = modal.querySelector('.tm-quote');
            const nameEl = modal.querySelector('.tm-name');
            const roleEl = modal.querySelector('.tm-role');
            const avatarWrap = modal.querySelector('.tm-avatar');
            const closeBtn = modal.querySelector('.tm-close');
            const backdrop = modal.firstElementChild;

            const open = (data) => {
                quoteEl.textContent = '“' + (data.quote || '') + '”';
                nameEl.textContent = data.name || '';
                roleEl.textContent = data.role || '';
                avatarWrap.innerHTML = data.avatar
                    ? '<img src=\"' + data.avatar + '\" alt=\"' + (data.name || '') + '\" class=\"h-full w-full object-cover\">'
                    : '';
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };
            const close = () => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            };
            closeBtn.addEventListener('click', close);
            backdrop.addEventListener('click', close);
            window.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });

            document.querySelectorAll('#testimonials-slider .tcard').forEach(card => {
                card.addEventListener('click', () => {
                    const data = {
                        name: card.getAttribute('data-name') || '',
                        role: card.getAttribute('data-role') || '',
                        quote: card.getAttribute('data-quote') || '',
                        avatar: card.getAttribute('data-avatar') || ''
                    };
                    open(data);
                });
            });
        });
    </script>
</section>
@endif


