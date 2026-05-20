<?php

namespace App\Filament\Resources\StockOuts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;


class StockOutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                ->schema([
                    Select::make('product_id')
                        ->label('Nama Barang')
                        ->relationship('product', 'name')
                        ->required(),
                    TextInput::make('quantity')
                        ->label('Jumlah Keluar')
                        ->numeric()
                        ->required(),
                    DatePicker::make('transaction_date')
                        ->label('Tanggal Transaksi')
                        ->date()
                        ->required(),
                    Textarea::make('note')
                        ->label('Catatan'),
                ])
                ->columns(2)
                ->columnSpanFull(),
            ]);
    }
}
