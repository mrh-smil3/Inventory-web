<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockIns extends ListRecords
{
    protected static string $resource = StockInResource::class;
    protected static ?string $title = 'Barang Masuk';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Tambah Barang Masuk'),
        ];
    }
}
