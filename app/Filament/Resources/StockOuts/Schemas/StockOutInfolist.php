<?php

namespace App\Filament\Resources\StockOuts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
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
                        // TextEntry::make('customer_name')
                        //     ->label('Pelanggan'),
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi'),
                        TextEntry::make('note')
                            ->label('Catatan')
                            ->columnSpanFull(),
                        RepeatableEntry::make('items')
                            ->label('Daftar Barang')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Nama Barang'),
                                TextEntry::make('quantity')
                                    ->label('Jumlah Keluar')
                                    ->numeric(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
