@php
    $isEdit = isset($company);
    $currentRating = old('stars', $company->stars ?? 0);
@endphp

<form action="{{ $isEdit ? route('dashboard.companies.update', $company->id) : route('dashboard.companies.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif

    {{-- الاسم --}}
    <div class="mb-3">
        <label class="form-label">Name (AR)</label>
        <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $company->name_ar ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Name (EN)</label>
        <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $company->name_en ?? '') }}" required>
    </div>

    {{-- العنوان --}}
    <div class="mb-3">
        <label class="form-label">Title (AR)</label>
        <input type="text" name="title_ar" class="form-control" value="{{ old('title_ar', $company->title_ar ?? '') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Title (EN)</label>
        <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $company->title_en ?? '') }}">
    </div>
    

    <!--{{-- البلد والرابط --}}-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Country</label>-->
    <!--    <input type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? '') }}">-->
    <!--</div>-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Link</label>-->
    <!--    <input type="url" name="link" class="form-control" value="{{ old('link', $company->link ?? '') }}">-->
    <!--</div>-->

    {{-- الوصف --}}
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Description (AR)</label>-->
    <!--    <textarea name="description_ar" class="form-control">{{ old('description_ar', $company->description_ar ?? '') }}</textarea>-->
    <!--</div>-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Description (EN)</label>-->
    <!--    <textarea name="description_en" class="form-control">{{ old('description_en', $company->description_en ?? '') }}</textarea>-->
    <!--</div>-->

    <!--{{-- اللائحة --}}-->
    <!--<div class="mb-3">-->
    <!--    <label class="form-label">Regulation</label>-->
    <!--    <textarea name="regulation" class="form-control">{{ old('regulation', $company->regulation ?? '') }}</textarea>-->
    <!--</div>-->

    {{-- ⭐ التقييم --}}

    {{-- التصنيف --}}
    <div class="mb-3">
                <label class="form-label fw-bold">@lang('influncer.category')</label>
        <input type="text" name="category" class="form-control" placeholder="@lang('influncer.followers')" min="0"
               value="{{ old('category', $company->category	 ?? 0) }}">
    
    </div>
     <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('influncer.followers')</label>
        <input type="text" name="number_of_followers" class="form-control" placeholder="@lang('influncer.followers')" min="0"
               value="{{ old('number_of_followers', $company->number_of_followers	 ?? 0) }}">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('influncer.voting')</label>
        <input type="number" name="count_vote" class="form-control" placeholder="@lang('influncer.voting')" min="0"
               value="{{ old('count_vote', $company->count_vote	 ?? 0) }}">
    </div>
    
        <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('influncer.orders')</label>
        <input type="number" name="orders" class="form-control" placeholder="@lang('influncer.orders')" min="0"
               value="{{ old('orders', $company->orders	 ?? 0) }}">
    </div>
        <div class="col-md-6">
          <label class="form-label">{{ __('speaker.followers_ticktock') }}</label>
          <input type="text" name="followers_ticktock" class="form-control" value="{{ old('followers_ticktock', $isEdit ? $company->followers_ticktock : '') }}">
        </div>
    {{-- الصورة --}}
    <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
        @if ($isEdit && $company->image)
            <img src="{{ asset('public/'.$company->image) }}" width="80" class="mt-2">
        @endif
    </div>

    {{-- زر الحفظ --}}
    <button type="submit" class="btn btn-primary">
        {{ $isEdit ? 'Update' : 'Save' }}
    </button>
</form>

{{-- ⭐ JavaScript لدعم التقييم --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('#star-rating .star');
        const hiddenInput = document.getElementById('stars');
        const ratingText = document.getElementById('rating-value');

        stars.forEach((star, index) => {
            star.addEventListener('mousemove', function (e) {
                const isHalf = e.offsetX < star.offsetWidth / 2;
                highlightStars(index, isHalf ? 0.5 : 1);
            });

            star.addEventListener('click', function (e) {
                const isHalf = e.offsetX < star.offsetWidth / 2;
                const rating = index + (isHalf ? 0.5 : 1);
                hiddenInput.value = rating;
                ratingText.textContent = rating;
            });
        });

        function highlightStars(index, partial) {
            stars.forEach((star, i) => {
                const icon = star.querySelector('i');
                if (i < index) {
                    icon.className = 'fas fa-star';
                } else if (i === index) {
                    icon.className = partial === 0.5 ? 'fas fa-star-half-alt' : 'fas fa-star';
                } else {
                    icon.className = 'far fa-star';
                }
            });
        }
    });
</script>
