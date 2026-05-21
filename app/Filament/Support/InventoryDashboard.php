<?php

namespace App\Filament\Support;

use Illuminate\Support\Number;

class InventoryDashboard
{
    public const LOW_STOCK_THRESHOLD = 10;

    public const MOVEMENT_DAYS = 30;

    public static function formatRupiah(float|int $amount): string
    {
        return Number::currency($amount, 'IDR', 'id');
    }

    public static function formatNumber(float|int $value): string
    {
        return Number::format($value, locale: 'id');
    }

    /**
     * @return array<int, string>
     */
    public static function lastDaysLabels(int $days = self::MOVEMENT_DAYS): array
    {
        return collect(range($days - 1, 0))
            ->map(fn (int $daysAgo) => now()->subDays($daysAgo)->format('d M'))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    public static function lastDaysKeys(int $days = self::MOVEMENT_DAYS): array
    {
        return collect(range($days - 1, 0))
            ->map(fn (int $daysAgo) => now()->subDays($daysAgo)->toDateString())
            ->all();
    }
}
