<?php

namespace App\Providers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Observers\StockObserver;
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
        StockIn::observe(StockObserver::class);
        StockOut::observe(StockObserver::class);
    }
}
