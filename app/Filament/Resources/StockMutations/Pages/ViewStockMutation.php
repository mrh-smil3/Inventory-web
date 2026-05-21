<?php

namespace App\Filament\Resources\StockMutations\Pages;

use App\Filament\Resources\StockMutations\StockMutationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewStockMutation extends ViewRecord
{
    protected static string $resource = StockMutationResource::class;

    protected static ?string $title = 'Detail Mutasi Stok';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
