<?php

namespace App\Filament\Resources\StockMutations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockMutationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('product.name')
                            ->label('Nama Barang'),
                        TextEntry::make('type')
                            ->label('Tipe Transaksi')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'in' => 'success',
                                'out' => 'danger',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'in' => 'Barang Masuk',
                                'out' => 'Barang Keluar',
                                default => $state,
                            }),
                        TextEntry::make('quantity')
                            ->label('Jumlah')
                            ->numeric(),
                        TextEntry::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->date(),
                        TextEntry::make('reference_id')
                            ->label('Reference ID (Sistem)'),
                        TextEntry::make('note')
                            ->label('Catatan')
                            ->columnSpanFull(),
                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
