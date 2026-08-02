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
                            ->validationMessages([
                                'required' => 'Nama Produk wajib diisi.',
                                'max' => 'Nama Produk tidak boleh lebih dari :max karakter.',
                            ])
                            ->columnSpanFull(),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->native()
                            ->required()
                            ->validationMessages([
                                'required' => 'Kategori wajib dipilih.',
                            ]),
                        TextInput::make('sku')
                            ->label('SKU')
                            ->helperText('Kode Unik Produk (Singkatan)')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'SKU wajib diisi.',
                                'max' => 'SKU tidak boleh lebih dari :max karakter.',
                                'unique' => 'SKU ini sudah digunakan oleh produk lain, silakan gunakan kode yang berbeda.',
                            ]),
                        TextInput::make('purchase_price')
                            ->label('Harga Beli')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live(onBlur: true)
                            ->validationMessages([
                                'required' => 'Harga Beli wajib diisi.',
                                'numeric' => 'Harga Beli harus berupa angka.',
                            ])
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
                            ])
                            ->validationMessages([
                                'required' => 'Harga Jual wajib diisi.',
                                'numeric' => 'Harga Jual harus berupa angka.',
                            ]),

                        TextInput::make('stock')
                            ->label('Stok Awal')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->validationMessages([
                                'numeric' => 'Stok Awal harus berupa angka.',
                                'min' => 'Stok Awal tidak boleh kurang dari :min.',
                            ]),

                        TextInput::make('min_stock')
                            ->label('Limit Stok Minimum')
                            ->helperText('Batas minimum stok yang tidak boleh dilewati saat stok keluar')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required()
                            ->validationMessages([
                                'required' => 'Limit Stok Minimum wajib diisi.',
                                'numeric' => 'Limit Stok Minimum harus berupa angka.',
                                'min' => 'Limit Stok Minimum tidak boleh kurang dari :min.',
                            ]),

                        Select::make('unit_id')
                            ->label('Satuan')
                            ->required()
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->validationMessages([
                                'required' => 'Satuan wajib dipilih.',
                            ])
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Satuan')
                                    ->required()
                                    ->maxLength(50)
                                    ->validationMessages([
                                        'required' => 'Nama Satuan wajib diisi.',
                                        'max' => 'Nama Satuan tidak boleh lebih dari :max karakter.',
                                    ]),
                            ]),

                    ])->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
