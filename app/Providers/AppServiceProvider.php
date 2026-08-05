<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockInItem;
use App\Models\StockOut;
use App\Models\StockOutItem;
use App\Observers\ProductObserver;
use App\Observers\StockInItemObserver;
use App\Observers\StockObserver;
use App\Observers\StockOutItemObserver;
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
        StockInItem::observe(StockInItemObserver::class);
        StockOut::observe(StockObserver::class);
        StockOutItem::observe(StockOutItemObserver::class);
        Product::observe(ProductObserver::class);
    }
}
