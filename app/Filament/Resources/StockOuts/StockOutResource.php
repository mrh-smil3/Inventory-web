<?php

namespace App\Filament\Resources\StockOuts;

use App\Filament\Resources\StockOuts\Pages\CreateStockOut;
use App\Filament\Resources\StockOuts\Pages\ListStockOuts;
use App\Filament\Resources\StockOuts\Pages\ViewStockOut;
use App\Filament\Resources\StockOuts\Schemas\StockOutForm;
use App\Filament\Resources\StockOuts\Schemas\StockOutInfolist;
use App\Filament\Resources\StockOuts\Tables\StockOutsTable;
use App\Models\StockMutation;
use App\Models\StockOut;
use BackedEnum;
use UnitEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

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
        ];
    }

    public static function cancelAction(): Action
    {
        return Action::make('cancel')
            ->label('Batalkan')
            ->icon(Heroicon::XCircle)
            ->color('danger')
            ->authorize('update')
            ->visible(fn (StockOut $record): bool => $record->status === 'completed')
            ->requiresConfirmation()
            ->modalHeading('Batalkan Barang Keluar')
            ->modalDescription('Transaksi ini akan dibatalkan dan stok produk yang telah dikeluarkan akan dikembalikan ke jumlah semula. Tindakan ini tidak dapat diurungkan.')
            ->modalSubmitActionLabel('Ya, Batalkan')
            ->action(function (StockOut $record): void {
                DB::transaction(function () use ($record): void {
                    foreach ($record->items as $item) {
                        $item->product?->increment('stock', $item->quantity);

                        StockMutation::where('reference_id', -$item->id)
                            ->where('type', 'out')
                            ->delete();
                    }

                    $record->update(['status' => 'cancelled']);
                });

                Notification::make()
                    ->title('Barang Keluar Dibatalkan')
                    ->body('Stok telah dikembalikan ke jumlah semula.')
                    ->success()
                    ->send();
            });
    }
}
