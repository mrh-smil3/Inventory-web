<?php

namespace App\Filament\Resources\StockIns;

use App\Filament\Resources\StockIns\Pages\CreateStockIn;
use App\Filament\Resources\StockIns\Pages\EditStockIn;
use App\Filament\Resources\StockIns\Pages\ListStockIns;
use App\Filament\Resources\StockIns\Pages\ViewStockIn;
use App\Filament\Resources\StockIns\Schemas\StockInForm;
use App\Filament\Resources\StockIns\Schemas\StockInInfolist;
use App\Filament\Resources\StockIns\Tables\StockInsTable;
use App\Models\StockIn;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class StockInResource extends Resource
{
    protected static ?string $model = StockIn::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowDownTray;

    protected static string | UnitEnum | null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Barang Masuk';

    public static function form(Schema $schema): Schema
    {
        return StockInForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockInInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockInsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockIns::route('/'),
            'create' => CreateStockIn::route('/create'),
            'view' => ViewStockIn::route('/{record}'),
            'edit' => EditStockIn::route('/{record}/edit'),
        ];
    }
}
