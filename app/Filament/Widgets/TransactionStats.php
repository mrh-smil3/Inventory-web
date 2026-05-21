<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StockIns\StockInResource;
use App\Filament\Resources\StockOuts\StockOutResource;
use App\Filament\Support\InventoryDashboard;
use App\Models\Category;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\Supplier;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStats extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = 'Aktivitas Transaksi';

    protected ?string $description = 'Pergerakan barang hari ini dan bulan berjalan';

    protected function getStats(): array
    {
        $today = today();

        $stockInToday = (int) StockIn::whereDate('transaction_date', $today)->sum('quantity');
        $stockOutToday = (int) StockOut::whereDate('transaction_date', $today)->sum('quantity');
        $stockInMonth = StockIn::whereMonth('transaction_date', $today->month)
            ->whereYear('transaction_date', $today->year)
            ->count();
        $stockOutMonth = StockOut::whereMonth('transaction_date', $today->month)
            ->whereYear('transaction_date', $today->year)
            ->count();

        return [
            Stat::make('Barang Masuk Hari Ini', InventoryDashboard::formatNumber($stockInToday).' unit')
                ->description('Total kuantitas masuk')
                ->descriptionIcon(Heroicon::OutlinedArrowDownTray)
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('success')
                ->url(StockInResource::getUrl('index')),
            Stat::make('Barang Keluar Hari Ini', InventoryDashboard::formatNumber($stockOutToday).' unit')
                ->description('Total kuantitas keluar')
                ->descriptionIcon(Heroicon::OutlinedArrowUpTray)
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('danger')
                ->url(StockOutResource::getUrl('index')),
            Stat::make('Transaksi Masuk Bulan Ini', InventoryDashboard::formatNumber($stockInMonth))
                ->description($today->translatedFormat('F Y'))
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->icon(Heroicon::OutlinedDocumentPlus)
                ->color('success')
                ->url(StockInResource::getUrl('index')),
            Stat::make('Transaksi Keluar Bulan Ini', InventoryDashboard::formatNumber($stockOutMonth))
                ->description($today->translatedFormat('F Y'))
                ->descriptionIcon(Heroicon::OutlinedCalendarDays)
                ->icon(Heroicon::OutlinedDocumentMinus)
                ->color('danger')
                ->url(StockOutResource::getUrl('index')),
            Stat::make('Kategori', InventoryDashboard::formatNumber(Category::count()))
                ->description('Kategori produk')
                ->descriptionIcon(Heroicon::OutlinedTag)
                ->icon(Heroicon::OutlinedTag)
                ->color('gray'),
            Stat::make('Supplier', InventoryDashboard::formatNumber(Supplier::count()))
                ->description('Pemasok aktif')
                ->descriptionIcon(Heroicon::OutlinedTruck)
                ->icon(Heroicon::OutlinedTruck)
                ->color('gray'),
        ];
    }
}
