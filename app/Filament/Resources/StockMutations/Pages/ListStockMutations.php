<?php

namespace App\Filament\Resources\StockMutations\Pages;

use App\Filament\Resources\StockMutations\StockMutationResource;
use Filament\Resources\Pages\ListRecords;

class ListStockMutations extends ListRecords
{
    protected static string $resource = StockMutationResource::class;

    protected static ?string $title = 'Daftar Mutasi Stok';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
