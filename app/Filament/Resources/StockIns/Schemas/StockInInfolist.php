<?php

namespace App\Filament\Resources\StockIns\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockInInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('No. Invoice'),
                        TextEntry::make('supplier.name')
                            ->label('Supplier'),
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
                                    ->label('Jumlah')
                                    ->numeric(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
