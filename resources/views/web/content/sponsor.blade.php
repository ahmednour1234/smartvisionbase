@extends('web.layouts.app')

@section('content')
@php $locale = app()->getLocale(); @endphp

<section class="breadcrumbs-custom bg-image context-dark" style="background-image: url({{ asset('web/assets/images/bg-breadcrumbs-01-1894x424.jpg') }});">
  <div class="container">
    <ul class="breadcrumbs-custom-path">
      <li><a href="{{ route('web.home') }}">Home</a></li>
      <li class="active">Sponsors</li>
    </ul>
    <h3 class="breadcrumbs-custom-title">{{ $sponsor_section->title[$locale] ?? '' }}</h3>
  </div>
</section>

<section class="section section-lg bg-default text-center">
  <div class="container">
    <h4 class="font-weight-bold mb-5">{{ $sponsor_section->title[$locale] ?? '' }}</h4>

    @foreach($categories as $category)
      <h4 class="text-primary mb-4">{{ $locale == 'ar' ? $category->name : $category->name_en }}</h4>
      <div class="row row-30 justify-content-center mb-5 sponsor-wrapper" data-id="{{ $category->id }}" data-offset="0"></div>
    @endforeach
  </div>
</section>

<section class="section section-lg bg-default text-center">
  <div class="container">
    <div class="block-lg block-center">
      <h6>Become a sponsor.</h6>
      <h2>Let’s become a part of our conference</h2>
      <a class="button button-lg button-primary button-offset-xl" href="{{ route('web.becomesponsor') }}" data-triangle=".button-overlay">
        <span>Become a sponsor</span><span class="button-overlay"></span>
      </a>
    </div>
  </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const wrapper = entry.target;
        const catId = wrapper.dataset.id;
        let offset = parseInt(wrapper.dataset.offset || 0);

        fetch(`/load-more-sponsors/${catId}?offset=${offset}`)
          .then(response => response.json())
          .then(data => {
            if (Array.isArray(data) && data.length > 0) {
              data.forEach(sponsor => {
                const col = document.createElement('div');
                col.className = 'col-md-6 col-lg-4';

                const image = sponsor.image ? sponsor.image.replace(/^\/+/, '') : '';
                const name = sponsor.name ? sponsor.name.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
                const title = sponsor.title ? sponsor.title.replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';

                // ✅ هنا بنضيف دائمًا public قبل الصورة
                const imageSrc = `https://forextraderssummit.iqbrandx.com/public/${image}`;

                col.innerHTML = `
                  <div class="sponsor-ritmo">
                    <a class="sponsor-ritmo-header" href="#" data-triangle=".sponsor-ritmo-overlay">
                      <div class="sponsor-ritmo-overlay"></div>
                      <div class="sponsor-ritmo-img">
                        <div class="sponsor-ritmo-img-default">
                          <img src="${imageSrc}" alt="${name}" style="width:120px; height:120px; object-fit: contain;">
                        </div>
                        <div class="sponsor-ritmo-img-active">
                          <img src="${imageSrc}" alt="${name}" style="width:120px; height:120px; object-fit: contain;">
                        </div>
                      </div>
                    </a>
                    <div class="sponsor-ritmo-body">
                      <h5 class="sponsor-ritmo-title"><span class="big">${name}</span></h5>
                      <p class="sponsor-ritmo-text">${title}</p>
                    </div>
                  </div>
                `;
                wrapper.appendChild(col);
              });

              wrapper.dataset.offset = offset + data.length;
              if (data.length >= 3) observer.observe(wrapper);
            }

            observer.unobserve(wrapper);
          })
          .catch(err => console.error("❌ Error loading more sponsors:", err));
      }
    });
  }, {
    rootMargin: "0px 0px 200px 0px",
    threshold: 0.1
  });

  document.querySelectorAll('.sponsor-wrapper').forEach(wrapper => {
    observer.observe(wrapper);
  });
});
</script>


@endsection
