<?php

namespace App\Filament\Resources\StockIns\Schemas;

use App\Models\Product;
use App\Models\StockIn;
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

class StockInForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('invoice_number')
                            ->label('No. Invoice')
                            ->unique(ignoreRecord: true)
                            ->required(),

                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->relationship('supplier', 'name')
                            ->required(),

                        DatePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->date()
                            ->required(),

                        Textarea::make('note')
                            ->label('Catatan')
                            ->columnSpanFull(),

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
                                    ->afterStateUpdated(function (Set $set, $state, $record) {
                                        if (! $record || ! $record->exists || ! $state || $record->product_id == $state) {
                                            return;
                                        }

                                        $oldProduct = Product::find($record->product_id);

                                        if ($oldProduct && $oldProduct->stock < $record->quantity) {
                                            Notification::make()
                                                ->title('Stok Tidak Mencukupi')
                                                ->body("Mengubah produk akan mengurangi stok produk lama ({$oldProduct->name}) sebanyak {$record->quantity}, yang akan membuatnya menjadi minus (stok saat ini: {$oldProduct->stock}).")
                                                ->danger()
                                                ->send();

                                            $set('product_id', $record->product_id);
                                        }
                                    })
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $productId = $state;

                                        if (! $productId) {
                                            return;
                                        }

                                        $product = Product::find($productId);

                                        if (! $product) {
                                            return;
                                        }

                                        $unitPrice = (float) $product->purchase_price;
                                        $quantity = (int) ($get('quantity') ?? 0);

                                        $set('unit_price', $unitPrice);
                                        $set('subtotal', $unitPrice * $quantity);
                                    }),

                                TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->numeric()
                                    ->minValue(0)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $unitPrice = (float) $state;
                                        $quantity = (int) ($get('quantity') ?? 0);

                                        $set('subtotal', $unitPrice * $quantity);
                                    }),

                                TextInput::make('quantity')
                                    ->label('Jumlah Masuk')
                                    ->numeric()
                                    ->minValue(1)
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Get $get, Set $set, $state, $record) {
                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', $unitPrice * (int) $state);

                                        if (! $record || ! $record->exists || ! $state) {
                                            return;
                                        }

                                        $productId = $get('product_id');

                                        if (! $productId) {
                                            return;
                                        }

                                        if ($record->product_id == $productId) {
                                            $product = Product::find($productId);
                                            $oldQuantity = $record->quantity;
                                            $newQuantity = (int) $state;

                                            if ($product && $newQuantity < $oldQuantity) {
                                                $reduction = $oldQuantity - $newQuantity;

                                                if ($product->stock < $reduction) {
                                                    Notification::make()
                                                        ->title('Stok Tidak Mencukupi')
                                                        ->body("Mengurangi jumlah masuk sebanyak {$reduction} akan membuat stok menjadi minus (stok saat ini: {$product->stock}).")
                                                        ->danger()
                                                        ->send();

                                                    $set('quantity', $oldQuantity);
                                                    $set('subtotal', $unitPrice * $oldQuantity);
                                                    return;
                                                }
                                            }
                                        } else {
                                            $oldProduct = Product::find($record->product_id);

                                            if ($oldProduct && $oldProduct->stock < $record->quantity) {
                                                Notification::make()
                                                    ->title('Stok Tidak Mencukupi')
                                                    ->body("Mengubah produk akan mengurangi stok produk lama ({$oldProduct->name}) sebanyak {$record->quantity}, yang akan membuatnya menjadi minus (stok saat ini: {$oldProduct->stock}).")
                                                    ->danger()
                                                    ->send();

                                                $set('product_id', $record->product_id);
                                                $set('quantity', $record->quantity);
                                                $set('subtotal', $unitPrice * $record->quantity);
                                                return;
                                            }
                                        }
                                    })
                                    ->rules([
                                        fn (Get $get, $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            if (! $record || ! $record->exists) {
                                                return;
                                            }

                                            $productId = $get('product_id');

                                            if (! $productId) {
                                                return;
                                            }

                                            if ($record->product_id == $productId) {
                                                $product = Product::find($productId);
                                                $oldQuantity = $record->quantity;
                                                $newQuantity = (int) $value;

                                                if ($product && $newQuantity < $oldQuantity) {
                                                    $reduction = $oldQuantity - $newQuantity;

                                                    if ($product->stock < $reduction) {
                                                        $fail("Mengurangi jumlah masuk akan membuat stok menjadi minus. Stok saat ini: {$product->stock}.");
                                                    }
                                                }
                                            } else {
                                                $oldProduct = Product::find($record->product_id);

                                                if ($oldProduct && $oldProduct->stock < $record->quantity) {
                                                    $fail("Mengubah produk akan membuat stok produk lama ({$oldProduct->name}) menjadi minus.");
                                                }
                                            }
                                        },
                                    ]),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->readOnly()
                                    ->live()
                                    ->dehydrated(),
                            ])
                            ->columns(4)
                            ->minItems(1)
                            ->addActionLabel('Tambah Barang')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }
}
