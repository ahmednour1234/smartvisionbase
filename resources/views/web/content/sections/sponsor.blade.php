@if ($section && $section->media_type === 'image')
    <!-- Section Official Sponsors Boxes -->
    <section class="sponsors-section section pt-5" style="background: #FFF;
    padding: 40px 0;
    ">
        <div class="container text-center">
          <h6 class="sub-tit">Meet Our Sponsors</h6>
            <h3 class="gre-title" style="font-size: 40px;font-weight: bolder;">{{ $section->description[$locale] ?? '' }}</h3>

            <div class="sponsors-grid mt-4">
                @foreach ($sponsors as $sponsor)
                    <div class="sponsor-box">
                        <img src="{{ asset('public/'.$sponsor->image) }}" alt="sponsor" />
                        <p style="margin-top: 10px; font-weight: 600; color: #E73701;">
                            {{ $locale == 'ar' ? $sponsor->category->name : $sponsor->category->name_en }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- زر عرض الكل --}}
            <div class="text-center mt-5">
                <a class="button button-secondary box-with-triangle-right wow fadeScale mt-4"
                   href="{{ route('web.sponsors') }}" data-triangle=".button-overlay">
                    <span>More Sponsors</span>
                    <span class="button-overlay"></span>
                </a>
            </div>
        </div>
    </section>

    <style>
        .sponsors-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            align-items: center;
            justify-items: center;
        }

        .sponsor-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            max-width: 400px;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .sponsor-box img {
            max-width: 100%;
            max-height: 100px;
            object-fit: contain;
            display: block;
            transition: transform 0.3s ease;
        }

        .sponsor-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .sponsor-box:hover img {
            transform: scale(1.1);
        }

        /* Responsive for tablets */
        @media (max-width: 991.98px) {
            .sponsors-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }
        }

        /* Responsive for mobiles */
        @media (max-width: 576px) {
            .sponsors-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }
            .sponsor-box {
                max-width: 100%;
                padding: 15px;
            }
            .sponsor-box img {
                max-height: 80px;
            }
        }
    </style>
@endif
