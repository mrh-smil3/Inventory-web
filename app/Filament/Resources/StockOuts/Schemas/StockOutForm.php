<?php

namespace App\Filament\Resources\StockOuts\Schemas;

use App\Models\Product;
use App\Models\StockOut;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class StockOutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->required()
                            ->readOnly()
                            ->default(function () {
                                $date = now()->format('Ymd');
                                $prefix = "INV/OUT/{$date}/";

                                $latest = StockOut::where('invoice_number', 'like', "{$prefix}%")
                                    ->orderBy('id', 'desc')
                                    ->first();

                                if ($latest) {
                                    $lastSeq = (int) substr($latest->invoice_number, -4);
                                    $nextSeq = str_pad($lastSeq + 1, 4, '0', STR_PAD_LEFT);
                                } else {
                                    $nextSeq = '0001';
                                }

                                return $prefix.$nextSeq;
                            }),

                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->date()
                            ->required(),
                        // TextInput::make('customer_name')
                        //     ->label('Pelanggan')
                        //     ->required(),
                        Textarea::make('note')
                            ->label('Catatan'),

                        Repeater::make('items')
                            ->label('Daftar Barang')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Nama Barang')
                                    ->relationship('product', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                    ->afterStateUpdated(function (Get $get) {
                                        $productId = $get('product_id');

                                        if (! $productId) {
                                            return;
                                        }

                                        $product = Product::find($productId);

                                        if ($product && $product->stock <= 0) {
                                            Notification::make()
                                                ->title('Peringatan: Stok Habis')
                                                ->body("Stok untuk produk {$product->name} saat ini kosong.")
                                                ->warning()
                                                ->send();
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->label('Jumlah Keluar')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state, $record) {
                                        $productId = $get('product_id');

                                        if (! $productId || ! $state) {
                                            return;
                                        }

                                        $product = Product::find($productId);

                                        if (! $product) {
                                            return;
                                        }

                                        $availableStock = $product->stock;

                                        if ($record && $record->exists && $record->product_id == $productId) {
                                            $availableStock += $record->quantity;
                                        }

                                        if ($state > $availableStock) {
                                            Notification::make()
                                                ->title('Stok Tidak Mencukupi')
                                                ->body("Stok tersedia untuk {$product->name}: {$availableStock}. Jumlah keluar yang diminta: {$state}.")
                                                ->danger()
                                                ->send();

                                            $set('quantity', $availableStock);
                                        }
                                    })
                                    ->rules([
                                        fn (Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $productId = $get('product_id');

                                            if (! $productId) {
                                                return;
                                            }

                                            $product = Product::find($productId);

                                            if (! $product) {
                                                return;
                                            }

                                            $availableStock = $product->stock;

                                            if ($record && $record->exists && $record->product_id == $productId) {
                                                $availableStock += $record->quantity;
                                            }

                                            if ($value > $availableStock) {
                                                $fail("Jumlah keluar ({$value}) melebihi stok yang tersedia ({$availableStock}).");
                                            }
                                        },
                                    ]),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->addActionLabel('Tambah Barang')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
