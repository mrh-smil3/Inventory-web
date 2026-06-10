<?php

namespace App\Filament\Resources\StockReports;

use App\Filament\Resources\StockReports\Pages\ListStockReports;
use App\Filament\Resources\StockReports\Tables\StockReportsTable;
use App\Models\Product;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Database\Eloquent\Builder;

class StockReportResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Laporan Stok';

    protected static ?string $pluralLabel = 'Laporan Stok';

    protected static ?string $modelLabel = 'Laporan Stok';

    public static function table(Table $table): Table
    {
        return StockReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockReports::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withSum('stockInItems as total_stock_in', 'quantity')
            ->withSum('stockOutItems as total_stock_out', 'quantity');
    }
}
