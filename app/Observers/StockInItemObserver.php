<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockInItem;
use App\Models\StockMutation;
use Exception;

class StockInItemObserver
{
    public function created(StockInItem $stockInItem): void
    {
        $stockInItem->product?->increment('stock', $stockInItem->quantity);

        StockMutation::create([
            'product_id' => $stockInItem->product_id,
            'type' => 'in',
            'quantity' => $stockInItem->quantity,
            'transaction_date' => $stockInItem->stockIn?->transaction_date,
            'reference_id' => $stockInItem->id,
            'stock_in_item_id' => $stockInItem->id,
            'note' => $stockInItem->stockIn?->note,
        ]);
    }

    public function updated(StockInItem $stockInItem): void
    {
        $oldQuantity = $stockInItem->getOriginal('quantity');
        $newQuantity = $stockInItem->quantity;
        $oldProductId = $stockInItem->getOriginal('product_id');
        $newProductId = $stockInItem->product_id;

        if ($oldProductId == $newProductId) {
            $diff = $newQuantity - $oldQuantity;
            $product = $stockInItem->product;

            if ($product) {
                if ($diff < 0 && $product->stock + $diff < 0) {
                    throw new Exception('Perubahan quantity barang masuk menyebabkan stok menjadi minus.');
                }

                $product->increment('stock', $diff);
            }
        } else {
            $oldProduct = Product::find($oldProductId);
            $newProduct = $stockInItem->product;

            if ($oldProduct) {
                if ($oldProduct->stock < $oldQuantity) {
                    throw new Exception('Perubahan produk barang masuk menyebabkan stok produk lama menjadi minus.');
                }

                $oldProduct->decrement('stock', $oldQuantity);
            }

            $newProduct?->increment('stock', $newQuantity);
        }

        StockMutation::updateOrCreate(
            [
                'reference_id' => $stockInItem->id,
                'type' => 'in',
            ],
            [
                'product_id' => $stockInItem->product_id,
                'quantity' => $stockInItem->quantity,
                'transaction_date' => $stockInItem->stockIn?->transaction_date,
                'stock_in_item_id' => $stockInItem->id,
                'note' => $stockInItem->stockIn?->note,
            ]
        );
    }

    public function deleted(StockInItem $stockInItem): void
    {
        $product = $stockInItem->product;

        if ($product) {
            if ($product->stock < $stockInItem->quantity) {
                throw new Exception('Tidak dapat menghapus item barang masuk karena akan menyebabkan stok menjadi minus.');
            }

            $product->decrement('stock', $stockInItem->quantity);
        }

        StockMutation::where('reference_id', $stockInItem->id)
            ->where('stock_in_item_id', $stockInItem->id)
            ->where('type', 'in')
            ->delete();
    }
}
