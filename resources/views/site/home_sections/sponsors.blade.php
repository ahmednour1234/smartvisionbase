@if(isset($sponsors) && $sponsors->count() > 0)
<section class="animate-in" data-direction="left">
    <style>
        .s-card { position: relative; overflow: hidden; }

        .s-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(145deg, rgba(220,38,38,.18), rgba(255,255,255,.02) 45%, rgba(0,0,0,.18));
            pointer-events: none;
        }

        .s-logo-wrap { min-height: 7rem; }

        @media (min-width: 768px) {
            .s-logo-wrap { min-height: 8rem; }
        }

        #sponsors-loader { display: none; }
        #sponsors-loader.active { display: flex; }
    </style>

    <div class="flex items-center gap-4 mb-6">
        <span class="h-px flex-1 bg-white/20"></span>
        <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">
            {{ __('Sponsors') }}
        </h2>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>

    <p class="mb-8 text-center text-sm md:text-base font-medium tracking-[0.2em] uppercase text-white/60">
        {{ __('Our Previous Clients') }}
    </p>

    <div class="mb-4 text-center">
        <span class="inline-flex items-center rounded-full bg-gradient-to-r from-red-600/70 via-black/70 to-red-700/70 px-4 py-1.5 text-sm md:text-base font-semibold text-white ring-1 ring-white/10 shadow">
            {{ __('Our Previous Clients') }}
        </span>
    </div>

    <div
        id="sponsors-wrapper"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8"
    >
        @include('site.sponsors.partials.items', [
            'items' => $sponsors->getCollection(),
            'title' => __('Our Previous Clients')
        ])
    </div>

    <div id="sponsors-loader" class="items-center justify-center mt-10 text-white/70 text-sm">
        {{ __('Loading...') }}
    </div>

    <div id="sponsors-scroll-trigger" class="h-10"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const wrapper = document.getElementById('sponsors-wrapper');
            const loader = document.getElementById('sponsors-loader');
            const trigger = document.getElementById('sponsors-scroll-trigger');

            if (!wrapper || !trigger) return;

            let page = 2;
            let loading = false;
            let hasMore = @json($sponsors->hasMorePages());

            function loadMoreSponsors() {
                if (loading || !hasMore) return;

                loading = true;
                loader?.classList.add('active');

                fetch(`{{ route('site.sponsors.load-more') }}?page=${page}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    wrapper.insertAdjacentHTML('beforeend', data.html);

                    hasMore = Boolean(data.has_more);
                    page = data.next_page;

                    if (!hasMore) {
                        trigger.remove();
                    }
                })
                .finally(() => {
                    loading = false;
                    loader?.classList.remove('active');
                });
            }

            const observer = new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    loadMoreSponsors();
                }
            }, {
                rootMargin: '300px'
            });

            if (hasMore) {
                observer.observe(trigger);
            }
        });
    </script>
</section>
@endif