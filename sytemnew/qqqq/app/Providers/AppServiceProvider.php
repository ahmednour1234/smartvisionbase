<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $appUrl = config('app.url');
        if ($appUrl) {
            $appUrl = rtrim($appUrl, '/');
            if (!str_ends_with($appUrl, '/public')) {
                $appUrl .= '/public';
            }
            URL::forceRootUrl($appUrl);
        }
    }
}
