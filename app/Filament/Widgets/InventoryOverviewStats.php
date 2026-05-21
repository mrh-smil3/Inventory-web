<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Support\InventoryDashboard;
use App\Models\Product;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class InventoryOverviewStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Ringkasan Inventori';

    protected ?string $description = 'Kondisi stok dan nilai persediaan saat ini';

    protected function getStats(): array
    {
        $productCount = Product::count();
        $totalStock = (int) Product::sum('stock');
        $inventoryCost = (float) Product::sum(DB::raw('stock * purchase_price'));
        $inventoryRetail = (float) Product::sum(DB::raw('stock * selling_price'));
        $outOfStock = Product::where('stock', 0)->count();
        $lowStock = Product::where('stock', '>', 0)
            ->where('stock', '<=', InventoryDashboard::LOW_STOCK_THRESHOLD)
            ->count();

        return [
            Stat::make('Total Produk', InventoryDashboard::formatNumber($productCount))
                ->description('Produk terdaftar')
                ->descriptionIcon(Heroicon::OutlinedShoppingBag)
                ->icon(Heroicon::OutlinedCube)
                ->url(ProductResource::getUrl('index')),
            Stat::make('Total Unit Stok', InventoryDashboard::formatNumber($totalStock))
                ->description('Jumlah unit di gudang')
                ->descriptionIcon(Heroicon::OutlinedArchiveBox)
                ->icon(Heroicon::OutlinedArchiveBox)
                ->color('info'),
            Stat::make('Nilai Inventori (HPP)', InventoryDashboard::formatRupiah($inventoryCost))
                ->description('Berdasarkan harga beli')
                ->descriptionIcon(Heroicon::OutlinedBanknotes)
                ->icon(Heroicon::OutlinedBanknotes)
                ->color('success'),
            Stat::make('Nilai Inventori (Jual)', InventoryDashboard::formatRupiah($inventoryRetail))
                ->description('Potensi omzet stok')
                ->descriptionIcon(Heroicon::OutlinedCurrencyDollar)
                ->icon(Heroicon::OutlinedCurrencyDollar)
                ->color('primary'),
            Stat::make('Stok Habis', InventoryDashboard::formatNumber($outOfStock))
                ->description('Produk tanpa stok')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->color($outOfStock > 0 ? 'danger' : 'success')
                ->url(ProductResource::getUrl('index')),
            Stat::make('Stok Menipis', InventoryDashboard::formatNumber($lowStock))
                ->description('Stok ≤ '.InventoryDashboard::LOW_STOCK_THRESHOLD.' unit')
                ->descriptionIcon(Heroicon::OutlinedBellAlert)
                ->icon(Heroicon::OutlinedBellAlert)
                ->color($lowStock > 0 ? 'warning' : 'success')
                ->url(ProductResource::getUrl('index')),
        ];
    }
}
