<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockOut extends ViewRecord
{
    protected static string $resource = StockOutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('print')
                ->label('Cetak')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->url(fn () => route('admin.stock-outs.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
