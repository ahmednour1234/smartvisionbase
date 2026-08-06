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

    {{-- البلد والرابط --}}
    <div class="mb-3">
        <label class="form-label">Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $company->country ?? '') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">Link</label>
        <input type="url" name="link" class="form-control" value="{{ old('link', $company->link ?? '') }}">
    </div>

    {{-- الوصف --}}
    <div class="mb-3">
        <label class="form-label">Description (AR)</label>
        <textarea name="description_ar" class="form-control">{{ old('description_ar', $company->description_ar ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Description (EN)</label>
        <textarea name="description_en" class="form-control">{{ old('description_en', $company->description_en ?? '') }}</textarea>
    </div>

    {{-- اللائحة --}}
    <div class="mb-3">
        <label class="form-label">Regulation</label>
        <textarea name="regulation" class="form-control">{{ old('regulation', $company->regulation ?? '') }}</textarea>
    </div>

    {{-- ⭐ التقييم --}}
    <div class="mb-3">
        <label class="form-label">Stars Rating</label>
        <div id="star-rating" style="font-size: 28px; cursor: pointer; color: #ffc107;">
            @for ($i = 1; $i <= 5; $i++)
                @php
                    $value = $i;
                    $class = 'far fa-star';

                    if ($currentRating >= $i) {
                        $class = 'fas fa-star';
                    } elseif ($currentRating >= $i - 0.5) {
                        $class = 'fas fa-star-half-alt';
                    }
                @endphp
                <span class="star" data-value="{{ $i }}"><i class="{{ $class }}"></i></span>
            @endfor
        </div>
        <input type="hidden" name="stars" id="stars" value="{{ $currentRating }}">
        <div class="mt-1 text-muted">Rating: <span id="rating-value">{{ $currentRating }}</span> / 5</div>
    </div>

    {{-- التصنيف --}}
    <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
            <option value="">Select category</option>
            <option value="Top 100 Member" {{ old('category', $company->category ?? '') === 'Top 100 Member' ? 'selected' : '' }}>Top 100 Member</option>
            <option value="Average" {{ old('category', $company->category ?? '') === 'Average' ? 'selected' : '' }}>Average</option>
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('company.voting')</label>
        <input type="number" name="count_vote" class="form-control" placeholder="@lang('company.voting')" min="0"
               value="{{ old('count_vote', $company->count_vote	 ?? 0) }}">
    </div>
        <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('company.orders')</label>
        <input type="number" name="orders" class="form-control" placeholder="@lang('company.orders')" min="0"
               value="{{ old('orders', $company->orders	 ?? 0) }}">
    </div>
    {{-- الصورة --}}
    <div class="mb-3">
        <label class="form-label">Image</label>
        <input type="file" name="image" class="form-control">
        @if ($isEdit && $company->image)
            <img src="{{ asset($company->image) }}" width="80" class="mt-2">
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
