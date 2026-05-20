<?php

namespace App\Filament\Resources\StockOuts;

use App\Filament\Resources\StockOuts\Pages\CreateStockOut;
use App\Filament\Resources\StockOuts\Pages\EditStockOut;
use App\Filament\Resources\StockOuts\Pages\ListStockOuts;
use App\Filament\Resources\StockOuts\Pages\ViewStockOut;
use App\Filament\Resources\StockOuts\Schemas\StockOutForm;
use App\Filament\Resources\StockOuts\Schemas\StockOutInfolist;
use App\Filament\Resources\StockOuts\Tables\StockOutsTable;
use App\Models\StockOut;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class StockOutResource extends Resource
{
    protected static ?string $model = StockOut::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ArrowUpTray;

    protected static string | UnitEnum | null $navigationGroup = 'Transaksi';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Barang Keluar';

    public static function form(Schema $schema): Schema
    {
        return StockOutForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StockOutInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockOutsTable::configure($table);
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
            'index' => ListStockOuts::route('/'),
            'create' => CreateStockOut::route('/create'),
            'view' => ViewStockOut::route('/{record}'),
            'edit' => EditStockOut::route('/{record}/edit'),
        ];
    }
}
