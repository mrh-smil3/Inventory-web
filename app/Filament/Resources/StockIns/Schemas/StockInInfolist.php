<?php

namespace App\Filament\Resources\StockIns\Schemas;

use Filament\Infolists\Components\TextEntry;
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
                        TextEntry::make('product.name')
                            ->label('Nama Barang'),
                        TextEntry::make('supplier.name')
                            ->label('Supplier'),
                        TextEntry::make('quantity')
                            ->label('Jumlah'),
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi'),
                        TextEntry::make('note')
                            ->label('Catatan'),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
