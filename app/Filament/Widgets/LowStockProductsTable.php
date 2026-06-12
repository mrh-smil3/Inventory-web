<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Products\ProductResource;
use App\Filament\Support\InventoryDashboard;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class LowStockProductsTable extends TableWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 6;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Produk Stok Rendah / Habis';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Product::query()
                ->with(['category', 'unit'])
                ->where('stock', '<=', InventoryDashboard::LOW_STOCK_THRESHOLD)
                ->orderBy('stock')
                ->orderBy('name'))
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable(),
                TextColumn::make('sku')
                    ->label('SKU'),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'danger',
                        $state <= InventoryDashboard::LOW_STOCK_THRESHOLD => 'warning',
                        default => 'success',
                    }),
                TextColumn::make('unit.name')
                    ->label('Satuan'),
                TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR'),
            ])
            ->emptyStateHeading('Semua stok aman')
            ->emptyStateDescription('Tidak ada produk dengan stok di bawah batas minimum.')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->headerActions([
                Action::make('viewAll')
                    ->label('Lihat Semua Produk')
                    ->url(ProductResource::getUrl('index'))
                    ->icon('heroicon-o-arrow-right'),
            ]);
    }
}
