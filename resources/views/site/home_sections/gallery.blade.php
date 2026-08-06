<section class="animate-in" data-direction="left">
    <div class="flex items-center gap-4 mb-4">
        <span class="h-px flex-1 bg-white/20"></span>
        <h2 class="text-3xl md:text-5xl font-semibold text-white text-center">{{ __('Past Events Gallery') }}</h2>
        <span class="h-px flex-1 bg-white/20"></span>
    </div>
    <div class="mt-4 columns-2 md:columns-3 lg:columns-4 gap-x-4 md:gap-x-6">
        @php
            $dir = public_path('uploads/media');
            $files = [];
            if (is_dir($dir)) {
                foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $path) {
                    if (is_file($path)) $files[] = 'uploads/media/' . basename($path);
                }
            }
            $files = array_slice($files, 0, 8);
        @endphp
        @forelse($files as $path)
            <div class="mb-4 break-inside-avoid">
                <a href="{{ asset($path) }}" target="_blank" rel="noopener" class="group relative block rounded-xl overflow-hidden bg-white/5 ring-1 ring-white/10 hover:ring-white/20 backdrop-blur-sm">
                    <img class="w-full h-auto object-contain transition-transform duration-300 group-hover:scale-[1.02]" src="{{ asset($path) }}" alt="" loading="lazy" decoding="async">
                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/30 to-transparent opacity-0 group-hover:opacity-100 transition"></div>
                </a>
            </div>
        @empty
            <div class="col-span-full text-gray-500">{{ __('No media uploaded yet.') }}</div>
        @endforelse
    </div>
</section>


