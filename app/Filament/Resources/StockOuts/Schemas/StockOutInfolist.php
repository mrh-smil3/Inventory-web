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
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->dateTime(),
                        TextEntry::make('note')
                            ->label('Catatan')
                            ->columnSpanFull(),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'cancelled' => 'Dibatalkan',
                                default => 'Selesai',
                            })
                            ->color(fn (string $state): string => match ($state) {
                                'cancelled' => 'danger',
                                default => 'success',
                            }),
                        RepeatableEntry::make('items')
                            ->label('Daftar Barang')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Nama Barang'),
                                TextEntry::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->prefix('Rp'),
                                TextEntry::make('quantity')
                                    ->label('Jumlah Keluar')
                                    ->numeric(),
                                TextEntry::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->prefix('Rp'),
                            ])
                            ->columns(4)
                            ->columnSpanFull(),
                        TextEntry::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->weight('bold'),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
