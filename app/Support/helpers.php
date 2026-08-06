<?php

use App\Models\Setting;
use Illuminate\Support\Facades\App;

if (!function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}

if (!function_exists('current_locale')) {
    function current_locale(): string
    {
        return App::getLocale() ?: config('app.locale');
    }
}

if (!function_exists('is_rtl')) {
    function is_rtl(): bool
    {
        return current_locale() === 'ar';
    }
}
