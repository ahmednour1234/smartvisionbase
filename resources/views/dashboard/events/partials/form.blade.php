<div class="rounded-xl border bg-white p-6 shadow-sm">
    <h2 class="text-sm font-semibold">Basic</h2>
    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Name (AR)</label>
            <input name="name_ar" value="{{ old('name_ar', optional($event)->name_ar) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('name_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Name (EN)</label>
            <input name="name_en" value="{{ old('name_en', optional($event)->name_en) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('name_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Slug</label>
            <input name="slug" value="{{ old('slug', optional($event)->slug) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('slug') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Cover Image</label>
            @php $cover = optional($event)->cover_image ?? optional($event)->image; @endphp
            @if($cover)
                <div class="mb-2">
                    <img src="{{ asset($cover) }}" alt="Current cover" class="h-24 rounded border object-cover">
                </div>
            @endif
            <input type="file" name="cover_image" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('cover_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700">Row Image (inline with text)</label>
            @if(optional($event)->row_image)
                <div class="mb-2">
                    <img src="{{ asset($event->row_image) }}" alt="Row image" class="h-24 rounded border object-cover">
                </div>
            @endif
            <input type="file" name="row_image" accept="image/*" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('row_image') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Start At</label>
            <input type="datetime-local" name="start_at" value="{{ old('start_at', optional(optional($event)->start_at)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('start_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">End At</label>
            <input type="datetime-local" name="end_at" value="{{ old('end_at', optional(optional($event)->end_at)->format('Y-m-d\TH:i')) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('end_at') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Website (AR)</label>
            <input name="website_url_ar" value="{{ old('website_url_ar', optional($event)->website_url_ar) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('website_url_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Website (EN)</label>
            <input name="website_url_en" value="{{ old('website_url_en', optional($event)->website_url_en) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
            @error('website_url_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" {{ old('is_active', optional($event)->is_active) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Active</span>
        </div>
        <div class="flex items-center gap-3">
            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', optional($event)->is_featured) ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
            <span class="text-sm text-gray-700">Featured</span>
        </div>
    </div>
</div>


