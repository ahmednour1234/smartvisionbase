<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Meeting;
use App\Policies\CompanyPolicy;
use App\Policies\MeetingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Company::class => CompanyPolicy::class,
        Meeting::class => MeetingPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}
