<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\LowStockNotifier;

class ProductObserver
{
    public function updated(Product $product): void
    {
        if (! $product->wasChanged('stock')) {
            return;
        }

        if ($product->isBelowLowStockThreshold()) {
            if (! $product->low_stock_notified_at) {
                LowStockNotifier::notify($product);
            }
        } elseif ($product->low_stock_notified_at) {
            $product->updateQuietly(['low_stock_notified_at' => null]);
        }
    }
}
