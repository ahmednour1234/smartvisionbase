<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ✅ أضف دول
use App\Repositories\Client\ClientRepositoryInterface;
use App\Repositories\Client\ClientRepository;

// ✅ كانوا موجودين عندك
use App\Repositories\Sponsor\SponsorRepositoryInterface;
use App\Repositories\Sponsor\SponsorRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Client
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);

        // Sponsor
        $this->app->bind(SponsorRepositoryInterface::class, SponsorRepository::class);
    }

    public function boot(): void {}
}
