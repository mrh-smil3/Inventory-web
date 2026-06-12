<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StockReportsTableWidget;
use Filament\Pages\Page;
use BackedEnum;
use UnitEnum;
use Filament\Support\Icons\Heroicon;


class StockReports extends Page
{
    protected static ?int $navigationSort = 7;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string | UnitEnum | null $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Stok';

    protected static ?string $title = 'Laporan Stok Barang';

    public function getHeaderWidgets(): array
    {
        return [
            StockReportsTableWidget::class,
        ];
    }
}
