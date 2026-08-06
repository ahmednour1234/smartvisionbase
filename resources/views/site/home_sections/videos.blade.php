@if(isset($videos) && $videos->count())
<section class="animate-in" data-direction="left">
    <div class="flex items-center gap-4 mb-6">
        <span class="h-px flex-1 bg-white/20"></span>
        <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">{{ __('Video Gallery') }}</h2>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>
    @php
        $grouped = $videos
            ->groupBy(fn($v) => $v->section ?: __('Videos'))
            ->sortBy(function ($items) { return $items->min('sort_order') ?? 0; });
        $ytId = function (string $url): string {
            $patterns = ['/youtu\\.be\\/([\\w-]+)/i','/v=([\\w-]+)/i','/embed\\/([\\w-]+)/i'];
            foreach ($patterns as $p) { if (preg_match($p, $url, $m)) return $m[1]; }
            return $url;
        };
    @endphp
    @foreach($grouped as $section => $items)
        <div class="mb-4 text-white/80 text-base md:text-lg">{{ $section }}</div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-6 mb-8">
            @foreach($items as $v)
                @php $id = $ytId($v->youtube_url); @endphp
                <div class="rounded-2xl overflow-hidden ring-1 ring-white/10 bg-white/5">
                    <div class="aspect-video">
                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $id }}" title="{{ $v->title }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
</section>
@endif


