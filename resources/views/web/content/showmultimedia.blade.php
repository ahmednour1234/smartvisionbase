@extends('web.layouts.app')

@section('content')
@php
    $locale = app()->getLocale();
@endphp

<section class="page-header">
    <div class="container">
        <div class="page-header__inner">
            <h2 id="typed-title">{{ $multi_media_category->name_en }}</h2>
        </div>
    </div>
</section>

<!-- Multimedia Section -->
<section class="section section-lg pt-5 pb-5">
    <div class="container">

        <!-- Videos -->
    <div class="mb-5">
  <h3 class="gradient-text text-center mb-4">{{ __('Videos') }}</h3>
  <div class="row justify-content-center g-4">

    @php
      // دالة محلية لاستخراج ID من كل الصيغ المعروفة
      $ytId = function ($url) {
          if (!$url) return null;
          $url = trim($url);

          // لو مدخل ID مباشر بطول 11
          if (preg_match('~^[a-zA-Z0-9_-]{11}$~', $url)) {
              return $url;
          }

          // ضَمَن بروتوكول
          if (!preg_match('~^https?://~i', $url)) {
              $url = 'https://' . ltrim($url, '/');
          }

          $parts = parse_url($url);
          $path  = $parts['path']  ?? '';
          $query = $parts['query'] ?? '';
          parse_str($query, $q);

          // watch?v=XXXX
          if (!empty($q['v']) && preg_match('~^[a-zA-Z0-9_-]{11}$~', $q['v'])) {
              return $q['v'];
          }

          // youtu.be/XXXX
          if (preg_match('~youtu\.be/([a-zA-Z0-9_-]{11})~', $url, $m)) {
              return $m[1];
          }

          // /embed/XXXX أو /shorts/XXXX أو /live/XXXX
          if (preg_match('~/(embed|shorts|live)/([a-zA-Z0-9_-]{11})~', $path, $m)) {
              return $m[2];
          }

          return null;
      };

      // مُعالج عام للـ links (JSON أو نص مفصول بسطر/فواصل)
      $normalizeLinks = function ($raw) {
          if (is_array($raw)) return $raw;
          if (is_string($raw)) {
              $arr = json_decode($raw, true);
              if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
                  return $arr;
              }
              // قسّم على سطور أو فواصل
              return preg_split('/[\r\n,]+/', $raw, -1, PREG_SPLIT_NO_EMPTY);
          }
          return [];
      };
    @endphp

    @foreach($multimedias as $media)
      @php $links = $normalizeLinks($media->links ?? []); @endphp

      @foreach($links as $link)
        @php
          $id = $ytId($link);
          $embedUrl = $id ? ('https://www.youtube-nocookie.com/embed/' . $id . '?rel=0') : null;
        @endphp

        @if($embedUrl)
          <div class="col-md-8 col-lg-6">
            <div class="ratio ratio-16x9">
              <iframe
                src="{{ $embedUrl }}"
                class="w-100 rounded shadow-sm"
                style="aspect-ratio:16/9"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                referrerpolicy="strict-origin-when-cross-origin"
                allowfullscreen>
              </iframe>
            </div>
          </div>
        @endif
      @endforeach
    @endforeach

    {{-- لو مفيش أي فيديو صالح --}}
    @if(collect($multimedias)->every(fn($m) => empty($normalizeLinks($m->links ?? []))))
      <div class="col-12 text-center text-muted">{{ __('No videos to show') }}</div>
    @endif

  </div>
</div>


        <!-- Images -->
        <div>
            <h3 class="gradient-text text-center mb-4">{{ __('Images') }}</h3>
            <div class="row justify-content-center g-4">
                @foreach($multimedias as $media)
                    @php
                        $images = is_array($media->images) ? $media->images : json_decode($media->images ?? '[]', true);
                    @endphp
                    @foreach($images as $image)
                        <div class="col-md-4 col-lg-3">
                            <a href="#" class="popup-trigger d-block" data-bs-toggle="modal" data-bs-target="#imageModal" data-image="{{ asset('public/'.$image) }}">
                                <div class="image-wrapper rounded overflow-hidden">
                                    <img src="{{ asset('public/'.$image) }}" class="img-fluid gallery-thumb" alt="image">
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

    </div>
</section>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-white border-0">
            <div class="modal-body text-center p-0">
                <img src="" class="img-fluid rounded w-100" id="popupImage" alt="popup image">
            </div>
        </div>
    </div>
</div>

<!-- Styles -->
<style>
    .page-header__inner {
        text-align: center;
        padding: 60px 0;
    }

    #typed-title {
        display: inline-block;
        overflow: hidden;
        white-space: normal;
        border-right: 3px solid #FFE986;
        font-family: 'Montserrat', sans-serif;
        font-size: 60px;
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0;
    }

    .gradient-text {
        background: linear-gradient(90deg, #FFE986 0%, #C48127 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }

    @media (max-width: 576px) {
        .section-title {
            font-size: 2.2rem;
        }

        #typed-title {
            font-size: 25px;
        }

        .page-header__inner {
            padding: 80px 0 20px;
        }
    }

    .gallery-thumb {
        height: 250px;
        object-fit: cover;
        width: 100%;
        transition: transform 0.3s ease;
    }

    .gallery-thumb:hover {
        transform: scale(1.03);
    }

    .image-wrapper {
        height: 250px;
        background: #f9f9f9;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 768px) {
        .gallery-thumb,
        .image-wrapper {
            height: 180px;
        }
    }
</style>

<!-- Script -->
<script>
    // Typewriter Effect (once)
    document.addEventListener("DOMContentLoaded", function () {
        const title = document.getElementById("typed-title");
        const text = title.textContent;
        title.textContent = "";
        let i = 0;

        function typeWriter() {
            if (i < text.length) {
                title.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 100);
            } else {
                title.style.borderRight = "none"; // remove cursor after finish
            }
        }

        typeWriter();
    });

    // Image Modal Preview
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        const imageUrl = trigger.getAttribute('data-image');
        const modalImage = imageModal.querySelector('#popupImage');
        modalImage.src = imageUrl;
    });
</script>
@endsection
