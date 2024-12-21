<?php

namespace App\Providers;

use App\Models\RollTransit;
use App\Observers\RollTransitObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RollTransit::observe(RollTransitObserver::class);

    }
}
