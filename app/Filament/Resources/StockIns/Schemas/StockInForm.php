<?php

namespace App\Filament\Resources\StockIns\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Forms\Components\DatePicker;

class StockInForm
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

                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->relationship('supplier', 'name')
                        ->required(),

                    TextInput::make('quantity')
                        ->label('Jumlah Masuk')
                        ->numeric()
                        ->required(),

                    DatePicker::make('transaction_date')
                        ->label('Tanggal Transaksi')
                        ->date()
                        ->required(),

                    Textarea::make('note')
                        ->label('Catatan')
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),
            ]);
    }
}
