@if ($section && $section->media_path)
    <section id="home-promo" class="overflow-hidden" style=" background: black; position: relative;">
        {{-- ✅ العنوان --}}
        <div class="container text-center py-3">
            <h2 class="promo-title" style="font-size: 60px;font-weight: 900;color: white;line-height: 1.2;margin-bottom: 10px;background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">
                {{ $section->title[app()->getLocale()] ?? 'Promo Title' }}
            </h2>
        </div>

        {{-- ✅ نوع الوسائط --}}
        @if ($section->media_type === 'video')
            <video autoplay muted loop playsinline
                   style="width: 100%; max-height: 90vh; object-fit: cover; display: block;">
                <source src="{{ asset('public/'.$section->media_path) }}" type="video/mp4">
                {{ __('home_sections.your_browser_does_not_support_video') }}
            </video>

        @elseif($section->media_type === 'image')
            <img src="{{ asset('public/'.$section->media_path) }}" alt="Promo"
                 style="width: 100%; height: 90vh; object-fit: cover; display: block;">

        @elseif($section->media_type === 'link' && $section->thumbnail)
            {{-- ✅ صورة + أيقونة تشغيل --}}
            <div class="position-relative" style="width: 100%; height: 90vh; cursor: pointer;"
                 data-bs-toggle="modal" data-bs-target="#videoModal">
                <img src="{{ asset('public/'.$section->thumbnail) }}" alt="Video Thumbnail"
                     style="width: 100%; height: 90vh; object-fit: cover; display: block;">
                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                    <div style="width: 80px; height: 80px; background: rgba(0,0,0,0.6); border-radius: 50%;
                                display: flex; align-items: center; justify-content: center;">
                        <svg width="30" height="30" fill="#fff" viewBox="0 0 16 16">
                            <path d="M6.79 5.093a.5.5 0 0 0-.79.407v5a.5.5 0 0 0 .79.407l4.5-2.5a.5.5 0 0 0 0-.814l-4.5-2.5z"/>
                            <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ✅ Modal --}}
            <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content bg-transparent border-0">
                        <div class="modal-body p-0">
                            <div class="ratio ratio-16x9">
                                <iframe id="videoFrame"
                                        src=""
                                        title="Embedded Video"
                                        frameborder="0"
                                        allow="autoplay; fullscreen"
                                        allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ Script --}}
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const modal = document.getElementById('videoModal');
                    const iframe = document.getElementById('videoFrame');
                    const rawUrl = @json($section->media_path);

                    function convertToEmbed(url) {
                        // YouTube: watch?v=
                        if (url.includes('youtube.com/watch?v=')) {
                            const videoId = new URL(url).searchParams.get("v");
                            return 'https://www.youtube.com/embed/' + videoId;
                        }

                        // YouTube short: youtu.be
                        if (url.includes('youtu.be/')) {
                            const videoId = url.split('/').pop();
                            return 'https://www.youtube.com/embed/' + videoId;
                        }

                        // Vimeo
                        if (url.includes('vimeo.com')) {
                            const videoId = url.split('/').pop();
                            return 'https://player.vimeo.com/video/' + videoId;
                        }

                        // Default: return as-is
                        return url;
                    }

                    modal.addEventListener('show.bs.modal', function () {
                        iframe.src = convertToEmbed(rawUrl) + '?autoplay=1';
                    });

                    modal.addEventListener('hidden.bs.modal', function () {
                        iframe.src = '';
                    });
                });
            </script>
        @endif
    </section>
@endif

{{-- ✅ Bootstrap --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
