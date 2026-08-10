<div class="row">
    <!-- الاسم بالعربية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.name_ar')</label>
        <input type="text" name="name_ar" class="form-control" placeholder="@lang('prizes.name_ar')"
               value="{{ old('name_ar', $prize->name_ar ?? '') }}" required>
    </div>

    <!-- الاسم بالإنجليزية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.name_en')</label>
        <input type="text" name="name_en" class="form-control" placeholder="@lang('prizes.name_en')"
               value="{{ old('name_en', $prize->name_en ?? '') }}" required>
    </div>

    <!-- العنوان بالعربية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.title_ar')</label>
        <input type="text" name="title_ar" class="form-control" placeholder="@lang('prizes.title_ar')"
               value="{{ old('title_ar', $prize->title_ar ?? '') }}">
    </div>

    <!-- العنوان بالإنجليزية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.title_en')</label>
        <input type="text" name="title_en" class="form-control" placeholder="@lang('prizes.title_en')"
               value="{{ old('title_en', $prize->title_en ?? '') }}">
    </div>

    <!-- الوصف بالعربية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.description_ar')</label>
        <textarea name="description_ar" class="form-control" rows="3"
                  placeholder="@lang('prizes.description_ar')">{{ old('description_ar', $prize->description_ar ?? '') }}</textarea>
    </div>

    <!-- الوصف بالإنجليزية -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.description_en')</label>
        <textarea name="description_en" class="form-control" rows="3"
                  placeholder="@lang('prizes.description_en')">{{ old('description_en', $prize->description_en ?? '') }}</textarea>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.order')</label>
        <input type="number" name="order" class="form-control" placeholder="@lang('prizes.order')" min="0"
               value="{{ old('order', $prize->order ?? 0) }}">
    </div>
    <!-- الصورة -->
    <div class="col-md-6 mb-3">
        <label class="form-label fw-bold">@lang('prizes.image')</label>
        <input type="file" name="img" class="form-control">
        @if(!empty($prize->img))
            <div class="mt-2">
                <img src="{{ asset($prize->img) }}" width="80" class="rounded shadow-sm border">
            </div>
        @endif
    </div>
</div>
