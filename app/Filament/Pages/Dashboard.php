<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InventoryOverviewStats;
use App\Filament\Widgets\LowStockProductsTable;
use App\Filament\Widgets\RecentStockMutationsTable;
use App\Filament\Widgets\StockByCategoryChart;
use App\Filament\Widgets\StockMovementChart;
use App\Filament\Widgets\TopOutboundProductsChart;
use App\Filament\Widgets\TransactionStats;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\AccountWidget;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class Dashboard extends BaseDashboard
{
    use HasPageShield;
    protected static ?string $title = 'Dashboard Inventori';

    protected static ?string $navigationLabel = 'Dashboard';

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AccountWidget::class,
            InventoryOverviewStats::class,
            TransactionStats::class,
            StockMovementChart::class,
            StockByCategoryChart::class,
            TopOutboundProductsChart::class,
            LowStockProductsTable::class,
            RecentStockMutationsTable::class,
        ];
    }

    /**
     * @return int | array<string, ?int>
     */
    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }
}
