<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Filament\Notifications\Notification;

class LowStockNotifier
{
    public static function notify(Product $product): void
    {
        Notification::make()
            ->title('Stok Menipis')
            ->body("{$product->name} tersisa {$product->stock} (limit {$product->min_stock}).")
            ->warning()
            ->sendToDatabase(User::all());

        $product->updateQuietly(['low_stock_notified_at' => now()]);
    }
}
