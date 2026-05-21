<?php

namespace App\Filament\Resources\StockOuts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockOutInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('No. Invoice'),
                        TextEntry::make('product.name')
                            ->label('Nama Barang'),
                        TextEntry::make('quantity')
                            ->label('Jumlah Keluar'),
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi'),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
