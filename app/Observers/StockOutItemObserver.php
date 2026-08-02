<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockMutation;
use App\Models\StockOutItem;
use Exception;

class StockOutItemObserver
{
    public function created(StockOutItem $stockOutItem): void
    {
        $product = $stockOutItem->product;

        if ($product) {
            if ($product->stock < $stockOutItem->quantity) {
                throw new Exception('Stok tidak mencukupi untuk melakukan transaksi barang keluar.');
            }

            if (($product->stock - $stockOutItem->quantity) < $product->min_stock) {
                throw new Exception("Transaksi ini akan membuat stok {$product->name} berada di bawah limit stok minimum ({$product->min_stock}).");
            }

            $product->decrement('stock', $stockOutItem->quantity);
        }

        StockMutation::create([
            'product_id' => $stockOutItem->product_id,
            'type' => 'out',
            'quantity' => $stockOutItem->quantity,
            'transaction_date' => $stockOutItem->stockOut?->transaction_date,
            'reference_id' => -$stockOutItem->id,
            'stock_out_item_id' => $stockOutItem->id,
            'note' => $stockOutItem->stockOut?->note,
        ]);
    }

    public function updated(StockOutItem $stockOutItem): void
    {
        $oldQuantity = $stockOutItem->getOriginal('quantity');
        $newQuantity = $stockOutItem->quantity;
        $oldProductId = $stockOutItem->getOriginal('product_id');
        $newProductId = $stockOutItem->product_id;

        if ($oldProductId == $newProductId) {
            $diff = $newQuantity - $oldQuantity;
            $product = $stockOutItem->product;

            if ($product) {
                if ($diff > 0 && $product->stock < $diff) {
                    throw new Exception('Stok tidak mencukupi untuk melakukan perubahan transaksi barang keluar.');
                }

                if ($diff > 0 && ($product->stock - $diff) < $product->min_stock) {
                    throw new Exception("Perubahan ini akan membuat stok {$product->name} berada di bawah limit stok minimum ({$product->min_stock}).");
                }

                if ($diff > 0) {
                    $product->decrement('stock', $diff);
                } elseif ($diff < 0) {
                    $product->increment('stock', abs($diff));
                }
            }
        } else {
            $oldProduct = Product::find($oldProductId);
            $newProduct = $stockOutItem->product;

            if ($newProduct) {
                if ($newProduct->stock < $newQuantity) {
                    throw new Exception('Stok tidak mencukupi pada produk baru untuk transaksi barang keluar.');
                }

                if (($newProduct->stock - $newQuantity) < $newProduct->min_stock) {
                    throw new Exception("Transaksi ini akan membuat stok {$newProduct->name} berada di bawah limit stok minimum ({$newProduct->min_stock}).");
                }

                $oldProduct?->increment('stock', $oldQuantity);
                $newProduct->decrement('stock', $newQuantity);
            }
        }

        StockMutation::updateOrCreate(
            [
                'reference_id' => -$stockOutItem->id,
                'type' => 'out',
            ],
            [
                'product_id' => $stockOutItem->product_id,
                'quantity' => $stockOutItem->quantity,
                'transaction_date' => $stockOutItem->stockOut?->transaction_date,
                'stock_out_item_id' => $stockOutItem->id,
                'note' => $stockOutItem->stockOut?->note,
            ]
        );
    }

    public function deleted(StockOutItem $stockOutItem): void
    {
        $stockOutItem->product?->increment('stock', $stockOutItem->quantity);

        StockMutation::where('reference_id', -$stockOutItem->id)
            ->where('stock_out_item_id', $stockOutItem->id)
            ->where('type', 'out')
            ->delete();
    }
}
