<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
 $this->app->bind(
        \App\Repositories\Speaker\SpeakerRepositoryInterface::class,
        \App\Repositories\Speaker\EloquentSpeakerRepository::class,
                \App\Repositories\Sponsor\EloquentSponsorRepository::class,
                \App\Repositories\Sponsor\SponsorRepositoryInterface::class,
        \App\Repositories\Client\ClientRepositoryInterface::class,
        \App\Repositories\Client\ClientRepository::class
    );  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
    {
        Relation::enforceMorphMap([
            'client'  => \App\Models\Client::class,
            'speaker' => \App\Models\Speaker::class,
            'sponsor' => \App\Models\Sponsor::class,
        ]);
    }
}
