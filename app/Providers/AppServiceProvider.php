<?php

namespace App\Providers;

use App\Models\OrderPunchDetail;
use App\Models\RollTransit;
use App\Observers\OrderPunchDetailObserver;
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
        OrderPunchDetail::observe(OrderPunchDetailObserver::class);

    }
}
