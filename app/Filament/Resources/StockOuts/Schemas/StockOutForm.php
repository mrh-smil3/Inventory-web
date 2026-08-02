<?php

namespace App\Filament\Resources\StockOuts\Schemas;

use App\Models\Product;
use App\Models\StockOut;
use Closure;
use Filament\Forms\Components\DateTimePicker;
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

                        DateTimePicker::make('transaction_date')
                            ->label('Tanggal Transaksi')
                            ->default(now())
                            ->required(),

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
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        $productId = $state;

                                        if (! $productId) {
                                            return;
                                        }

                                        $product = Product::find($productId);

                                        if (! $product) {
                                            return;
                                        }

                                        if ($product->stock <= 0) {
                                            Notification::make()
                                                ->title('Peringatan: Stok Habis')
                                                ->body("Stok untuk produk {$product->name} saat ini kosong.")
                                                ->warning()
                                                ->send();
                                        } elseif ($product->stock <= $product->min_stock) {
                                            Notification::make()
                                                ->title('Peringatan: Stok Menipis')
                                                ->body("Stok untuk produk {$product->name} sudah berada pada atau di bawah limit stok minimum ({$product->min_stock}).")
                                                ->warning()
                                                ->send();
                                        }

                                        $unitPrice = (float) $product->selling_price;
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
                                        } elseif (($availableStock - $state) < $product->min_stock) {
                                            Notification::make()
                                                ->title('Melebihi Limit Stok Minimum')
                                                ->body("Jumlah keluar ini akan membuat stok {$product->name} menjadi ".($availableStock - $state).", di bawah limit stok minimum ({$product->min_stock}).")
                                                ->danger()
                                                ->send();
                                        }

                                        $unitPrice = (float) ($get('unit_price') ?? 0);
                                        $set('subtotal', $unitPrice * (int) $state);
                                    })
                                    ->rules([
                                        fn (Get $get, $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record): void {
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

                                                return;
                                            }

                                            if (($availableStock - $value) < $product->min_stock) {
                                                $fail("Jumlah keluar ({$value}) melebihi limit stok minimum untuk {$product->name}. Sisa stok tidak boleh kurang dari {$product->min_stock}.");
                                            }
                                        },
                                    ])
                                    // Same threshold check as the rule above, surfaced as an inline
                                    // hint under the field itself (not just a toast notification),
                                    // so the limit is visible right where the user is typing.
                                    ->hintColor('danger')
                                    ->hint(function (Get $get, $record, $state): ?string {
                                        $productId = $get('product_id');

                                        if (! $productId || ! $state) {
                                            return null;
                                        }

                                        $product = Product::find($productId);

                                        if (! $product) {
                                            return null;
                                        }

                                        $availableStock = $product->stock;

                                        if ($record && $record->exists && $record->product_id == $productId) {
                                            $availableStock += $record->quantity;
                                        }

                                        $quantity = (int) $state;

                                        if ($quantity > $availableStock) {
                                            return "Melebihi stok tersedia ({$availableStock}).";
                                        }

                                        if (($availableStock - $quantity) < $product->min_stock) {
                                            return 'Sisa stok akan menjadi '.($availableStock - $quantity)." (di bawah limit minimum {$product->min_stock}).";
                                        }

                                        return null;
                                    }),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->readOnly()
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
