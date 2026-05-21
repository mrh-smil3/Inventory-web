<?php

namespace App\Filament\Resources\StockMutations;

use App\Filament\Resources\StockMutations\Pages\ListStockMutations;
use App\Filament\Resources\StockMutations\Pages\ViewStockMutation;
use App\Filament\Resources\StockMutations\Schemas\StockMutationInfolist;
use App\Filament\Resources\StockMutations\Tables\StockMutationsTable;
use App\Models\StockMutation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class StockMutationResource extends Resource
{
    protected static ?string $model = StockMutation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Mutasi Stok';

    protected static ?string $pluralLabel = 'Mutasi Stok';

    protected static ?string $modelLabel = 'Mutasi Stok';

    public static function infolist(Schema $schema): Schema
    {
        return StockMutationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StockMutationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMutations::route('/'),
            'view' => ViewStockMutation::route('/{record}'),
        ];
    }
}
