@if ($section && $section->media_type === 'image')
<section class="parallax-container section mt-5" style="background: #f5f5f5;">
    <div class="parallax-content section-lg context-dark text-center">
        <div class="container">
            <h6 class="sub-tit">
                {{ $section->title[$locale] ?? '' }}
            </h6>
            <h3 class="gre-title" style="font-size: 40px;font-weight: bolder; margin-top: 15px">{{ $section->description[$locale] ?? '' }}</h3>

            <div class="row mt-4">
                @php
                    $socialPlatforms = [
                        'facebook' => 'facebook-f',
                        'twitter' => 'twitter',
                        'linkedin' => 'linkedin',
                        'youtube' => 'youtube-play',
                        'tiktok' => 'twitter',
                        'instagram' => 'instagram',
                    ];
                @endphp

                @foreach ($speakers as $speaker)
                    <div class="col-6 col-md-6 col-lg-4 mb-4">
                        <div class="speaker">
                            <div class="speaker-img">
                                <a href="#">
                                    <img src="{{ asset('public/'.$speaker->image) }}"
                                        alt="{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}" />
                                </a>
                            </div>
                            <div class="speaker-info">
                                <h5 class="speaker-title">
                                    <a href="#">{{ $locale == 'ar' ? $speaker->name_ar : $speaker->name_en }}</a>
                                </h5>
                                <p class="speaker-position">
                                    {{ $locale == 'ar' ? $speaker->title_ar : $speaker->title_en }}
                                </p>

                                <ul class="speaker-social-list">
                                    @foreach ($socialPlatforms as $field => $icon)
                                        @if (!empty($speaker->$field))
                                            <li>
                                                <a class="icon fa fa-{{ $icon }}" href="{{ $speaker->$field }}" target="_blank"
                                                    rel="noopener noreferrer"></a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a class="button button-secondary box-with-triangle-right wow fadeScale mt-4"
                href="{{ route('web.speaker') }}" data-triangle=".button-overlay">
                <span>{{ __('home.view_all_speakers') }}</span>
                <span class="button-overlay"></span>
            </a>
        </div>
    </div>
</section>

<style>
    .speaker {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        display: flex;
        flex-direction: column;
        cursor: pointer;
        height: 100%;
        background: #fff;
        transition: box-shadow 0.3s ease, transform 0.3s ease;
        border: 1px solid #e0e0e0;
    }
    .speaker::before {
        content: "";
        position: absolute;
        top: 8px;
        left: 8px;
        right: 8px;
        bottom: 8px;
        border-radius: 10px;
        border: 2px solid rgba(204, 37, 46, 0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
        z-index: 1;
    }
    .speaker:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(204, 37, 46, 0.2);
    }
    .speaker:hover::before {
        opacity: 1;
    }
    .speaker-img {
        background: #fff;
        padding: 15px;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
    .speaker-img img {
        width: 100%;
        height: auto;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.3s ease;
        display: block;
    }
    .speaker:hover .speaker-img img {
        transform: scale(1.05);
    }
    .speaker-info {
        background: #f7f7f7;
        padding: 20px 15px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
    }
    .speaker-title {
        font-weight: 700;
        font-size: 1.5rem;
        color: #222;
        margin-bottom: 8px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .speaker-position {
        color: #666;
        font-size: 1rem;
        margin-bottom: 12px;
        word-wrap: break-word;
    }
    .speaker-social-list {
        list-style: none;
        padding: 0;
        margin: 0 auto;
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: auto;
    }
    .speaker-social-list li a {
        color: #cc252e;
        font-size: 18px;
        transition: color 0.3s ease;
    }
    .speaker-social-list li a:hover {
        color: #e84b3a;
    }
    @media (max-width: 991.98px) {
        .speaker-img {
            padding: 10px;
        }
        .speaker-info {
            padding: 0;
        }
        .speaker-title {
            font-size: 1.2rem;
            white-space: normal;
        }
        .speaker-position {
            font-size: 0.9rem;
        }
    }
    @media (max-width: 576px) {
        .speaker-img {
            padding: 8px;
        }
        .speaker-title {
            font-size: 1rem;
        }
        .speaker-position {
            font-size: 0.85rem;
        }
    }
</style>
@endif
