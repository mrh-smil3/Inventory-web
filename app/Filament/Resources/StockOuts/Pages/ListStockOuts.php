<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockOuts extends ListRecords
{
    protected static string $resource = StockOutResource::class;

    protected static ?string $title = 'Barang Keluar';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Tambah Barang Keluar'),
        ];
    }
}
