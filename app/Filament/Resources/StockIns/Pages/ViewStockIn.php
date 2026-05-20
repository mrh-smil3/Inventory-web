<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockIn extends ViewRecord
{
    protected static string $resource = StockInResource::class;
    protected static ?string $title = 'Detail Barang Masuk';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
