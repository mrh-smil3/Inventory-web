<?php

namespace App\Filament\Resources\Products\Schemas;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->native()
                            ->required(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->helperText('Kode Unik Produk (Singkatan)')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),
                        TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live(onBlur: true)
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                    if (is_numeric($value) && is_numeric($get('selling_price')) && (float) $value > (float) $get('selling_price')) {
                                        $fail('Harga Beli tidak boleh lebih besar dari Harga Jual');
                                    }
                                },
                            ]),

                        TextInput::make('selling_price')
                            ->label('Harga Jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, ?string $state): void {
                                if (is_numeric($state) && is_numeric($get('purchase_price')) && (float) $state < (float) $get('purchase_price')) {
                                    Notification::make()
                                        ->title('Harga Jual harus lebih besar dari Harga Beli')
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->rules([
                                fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get): void {
                                    if (is_numeric($value) && is_numeric($get('purchase_price')) && (float) $value < (float) $get('purchase_price')) {
                                        $fail('Harga Jual harus lebih besar atau sama dengan Harga Beli');
                                    }
                                },
                            ]),

                        TextInput::make('stock')
                            ->label('Stok Awal')
                            ->numeric()
                            ->default(0),

                        Select::make('unit_id')
                            ->label('Satuan')
                            ->required()
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Satuan')
                                    ->required()
                                    ->maxLength(50),
                            ]),

                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
