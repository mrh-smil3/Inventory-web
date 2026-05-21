<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

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
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }

                                $set('slug', Str::slug($state));
                            }),
                        TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->native()
                            ->required(),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->helperText('Kode Unik Produk (Singkatan)')
                            ->required()
                            ->maxLength(50),
                        TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('selling_price')
                            ->label('Harga Jual')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('selling_price') < $get('purchase_price'))) {
                                    Notification::make()
                                        ->title('Harga Jual harus lebih besar dari Harga Beli')
                                        ->danger()
                                        ->send();
                                    $set('selling_price', $get('purchase_price'));
                                }

                            }),

                        TextInput::make('stock')
                            ->label('Stok Awal')
                            ->numeric()
                            ->required(),

                        Select::make('unit_id')
                            ->label('Unit')
                            ->required()
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Unit')
                                    ->required()
                                    ->maxLength(50),
                            ]),

                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
