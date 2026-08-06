@extends('layouts.dashboard')
@section('title', 'Settings')
@section('page_title', 'Settings')

@section('content')
    <div class="mx-auto max-w-4xl">
        <form method="POST" action="{{ route('dashboard.settings.update') }}" class="space-y-8" enctype="multipart/form-data">
            @csrf
            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Site</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Site Name (AR)</label>
                        <input name="site_name_ar" value="{{ old('site_name_ar', $settings['site_name_ar']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('site_name_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Site Name (EN)</label>
                        <input name="site_name_en" value="{{ old('site_name_en', $settings['site_name_en']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('site_name_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Logo Upload</label>
                        <input type="file" name="logo_upload" accept=".png,.jpg,.jpeg,.svg,.webp" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @if(!empty($settings['logo_upload']) || !empty($settings['logo']))
                            <div class="mt-3 flex items-center gap-3">
                                <span class="text-xs text-gray-600">Current:</span>
                                @php
                                    $currentLogo = $settings['logo_upload'] ?: $settings['logo'];
                                    $currentLogoUrl = \Illuminate\Support\Str::startsWith($currentLogo, ['http://','https://','//']) ? $currentLogo : asset($currentLogo);
                                @endphp
                                <img src="{{ $currentLogoUrl }}" alt="Current Logo" class="h-10 w-auto object-contain border rounded-md p-1 bg-white">
                            </div>
                        @endif
                        @error('logo_upload') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Logo Path (optional)</label>
                        <input name="logo" value="{{ old('logo', $settings['logo']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('logo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Contact</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input name="phone" value="{{ old('phone', $settings['phone']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('phone') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">WhatsApp</label>
                        <input name="whatsapp" value="{{ old('whatsapp', $settings['whatsapp']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('whatsapp') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" value="{{ old('email', $settings['email']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address (EN)</label>
                        <input name="address_en" value="{{ old('address_en', $settings['address_en']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('address_en') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Address (AR)</label>
                        <input name="address_ar" value="{{ old('address_ar', $settings['address_ar']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('address_ar') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Social</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Facebook</label>
                        <input name="facebook" value="{{ old('facebook', $settings['facebook']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('facebook') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Instagram</label>
                        <input name="instagram" value="{{ old('instagram', $settings['instagram']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('instagram') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">X (Twitter)</label>
                        <input name="x" value="{{ old('x', $settings['x']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('x') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">LinkedIn</label>
                        <input name="linkedin" value="{{ old('linkedin', $settings['linkedin']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('linkedin') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">TikTok</label>
                        <input name="tiktok" value="{{ old('tiktok', $settings['tiktok']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('tiktok') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">YouTube</label>
                        <input name="youtube" value="{{ old('youtube', $settings['youtube']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('youtube') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm">
                <h2 class="text-sm font-semibold">Map</h2>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="number" step="any" name="map_lat" value="{{ old('map_lat', $settings['map_lat']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('map_lat') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="number" step="any" name="map_lng" value="{{ old('map_lng', $settings['map_lng']) }}" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"/>
                        @error('map_lng') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-xl border bg-white p-6 shadow-sm dash-card">
                <h2 class="form-section-title">Home Sections Order</h2>
                <p class="mt-2 text-sm text-gray-600">Arrange sections by order (top to bottom). Put each key on a new line. Available keys: hero, upcoming, about, testimonials, cta, faq, videos, gallery, sponsors. Comma-separated values are also supported. Any known section not listed here will be appended automatically.</p>
                @php
                    $defaultHomeSections = "hero\nvideos\nupcoming\nabout\ntestimonials\ncta\nsponsors\nfaq\ngallery";
                @endphp
                <textarea name="home_sections" rows="6" class="mt-3 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('home_sections', $settings['home_sections'] ?? $defaultHomeSections) }}</textarea>
                @error('home_sections') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <button class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
@endsection


