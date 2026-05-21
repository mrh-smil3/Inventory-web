<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockMutation;
use App\Models\StockOut;
use Exception;

class StockObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(mixed $model): void
    {
        if ($model instanceof StockIn) {
            $product = $model->product;
            if ($product) {
                $product->increment('stock', $model->quantity);
            }

            StockMutation::create([
                'product_id' => $model->product_id,
                'type' => 'in',
                'quantity' => $model->quantity,
                'transaction_date' => $model->transaction_date,
                'reference_id' => $model->id,
                'note' => $model->note,
            ]);
        } elseif ($model instanceof StockOut) {
            $product = $model->product;
            if ($product) {
                if ($product->stock < $model->quantity) {
                    throw new Exception('Stok tidak mencukupi untuk melakukan transaksi barang keluar.');
                }
                $product->decrement('stock', $model->quantity);
            }

            StockMutation::create([
                'product_id' => $model->product_id,
                'type' => 'out',
                'quantity' => $model->quantity,
                'transaction_date' => $model->transaction_date,
                'reference_id' => -$model->id,
                'note' => $model->note,
            ]);
        }
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(mixed $model): void
    {
        if ($model instanceof StockIn) {
            $oldQuantity = $model->getOriginal('quantity');
            $newQuantity = $model->quantity;
            $oldProductId = $model->getOriginal('product_id');
            $newProductId = $model->product_id;

            if ($oldProductId == $newProductId) {
                $diff = $newQuantity - $oldQuantity;
                $product = $model->product;
                if ($product) {
                    // Check if decrementing would cause negative stock
                    if ($diff < 0 && $product->stock + $diff < 0) {
                        throw new Exception('Perubahan quantity barang masuk menyebabkan stok menjadi minus.');
                    }
                    $product->increment('stock', $diff);
                }
            } else {
                // Product changed
                $oldProduct = Product::find($oldProductId);
                $newProduct = $model->product;

                // Decrement old product's stock by old quantity
                if ($oldProduct) {
                    if ($oldProduct->stock < $oldQuantity) {
                        throw new Exception('Perubahan produk barang masuk menyebabkan stok produk lama menjadi minus.');
                    }
                    $oldProduct->decrement('stock', $oldQuantity);
                }

                // Increment new product's stock by new quantity
                if ($newProduct) {
                    $newProduct->increment('stock', $newQuantity);
                }
            }

            // Update mutation
            StockMutation::updateOrCreate(
                ['reference_id' => $model->id],
                [
                    'product_id' => $model->product_id,
                    'type' => 'in',
                    'quantity' => $model->quantity,
                    'transaction_date' => $model->transaction_date,
                    'note' => $model->note,
                ]
            );

        } elseif ($model instanceof StockOut) {
            $oldQuantity = $model->getOriginal('quantity');
            $newQuantity = $model->quantity;
            $oldProductId = $model->getOriginal('product_id');
            $newProductId = $model->product_id;

            if ($oldProductId == $newProductId) {
                $diff = $newQuantity - $oldQuantity;
                $product = $model->product;
                if ($product) {
                    if ($product->stock < $diff) {
                        throw new Exception('Stok tidak mencukupi untuk melakukan perubahan transaksi barang keluar.');
                    }
                    $product->decrement('stock', $diff);
                }
            } else {
                // Product changed
                $oldProduct = Product::find($oldProductId);
                $newProduct = $model->product;

                // Increment old product's stock back by old quantity
                if ($oldProduct) {
                    $oldProduct->increment('stock', $oldQuantity);
                }

                // Decrement new product's stock by new quantity
                if ($newProduct) {
                    if ($newProduct->stock < $newQuantity) {
                        throw new Exception('Stok tidak mencukupi pada produk baru untuk transaksi barang keluar.');
                    }
                    $newProduct->decrement('stock', $newQuantity);
                }
            }

            // Update mutation
            StockMutation::updateOrCreate(
                ['reference_id' => -$model->id],
                [
                    'product_id' => $model->product_id,
                    'type' => 'out',
                    'quantity' => $model->quantity,
                    'transaction_date' => $model->transaction_date,
                    'note' => $model->note,
                ]
            );
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(mixed $model): void
    {
        if ($model instanceof StockIn) {
            $product = $model->product;
            if ($product) {
                if ($product->stock < $model->quantity) {
                    throw new Exception('Tidak dapat menghapus transaksi barang masuk karena akan menyebabkan stok menjadi minus.');
                }
                $product->decrement('stock', $model->quantity);
            }

            StockMutation::where('reference_id', $model->id)->delete();

        } elseif ($model instanceof StockOut) {
            $product = $model->product;
            if ($product) {
                $product->increment('stock', $model->quantity);
            }

            StockMutation::where('reference_id', -$model->id)->delete();
        }
    }
}
