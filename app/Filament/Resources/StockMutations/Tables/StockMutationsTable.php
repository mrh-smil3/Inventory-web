<?php

namespace App\Filament\Resources\StockMutations\Tables;

use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMutationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe Transaksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit_price')
                    ->label('Harga Satuan')
                    ->getStateUsing(fn ($record): ?float => match ($record->type) {
                        'in' => $record->stockInItem?->unit_price,
                        'out' => $record->stockOutItem?->unit_price,
                        default => null,
                    })
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('-'),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->getStateUsing(fn ($record): ?float => match ($record->type) {
                        'in' => $record->stockInItem?->subtotal,
                        'out' => $record->stockOutItem?->subtotal,
                        default => null,
                    })
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('-'),
                TextColumn::make('transaction_date')
                    ->label('Tanggal Transaksi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('note')
                    ->label('Catatan')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([])
            ->defaultSort('transaction_date', 'desc');
    }
}
