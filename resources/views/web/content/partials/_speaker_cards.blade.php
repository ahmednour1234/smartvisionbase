@php
    $locale = app()->getLocale();

    $socialPlatforms = [
        'facebook'  => 'facebook-f',
        'twitter'   => 'twitter',
        'linkedin'  => 'linkedin',
        'youtube'   => 'youtube-play',
        'tiktok'    => 'twitter',
        'instagram' => 'instagram',
    ];
@endphp

@foreach ($speakers as $speaker)
    <div class="col-6 col-md-6 col-lg-4 mb-4" id="speaker-{{ $speaker->id }}">
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
                                <a class="icon fa fa-{{ $icon }}"
                                   href="{{ $speaker->$field }}"
                                   target="_blank"
                                   rel="noopener noreferrer"></a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endforeach
