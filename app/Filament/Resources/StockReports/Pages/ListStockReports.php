<?php

namespace App\Filament\Resources\StockReports\Pages;

use App\Filament\Resources\StockReports\StockReportResource;
use Filament\Resources\Pages\ListRecords;

class ListStockReports extends ListRecords
{
    protected static string $resource = StockReportResource::class;

    protected static ?string $title = 'Laporan Stok Barang';

    protected function getHeaderActions(): array
    {
        return [];
    }
}
