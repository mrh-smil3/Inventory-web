<?php

namespace App\Observers;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockMutation;
class StockObserver
{
    /**
     * Handle the Stock "created" event.
     */
    public function created(Stock $stock): void
    {
        StockMutation::create([
            'product_id' => $stockIn->product_id,
            'type' => 'in',
            'quantity' => $stockIn->quantity,
            'transaction_date' => $stockIn->transaction_date,
            'reference_id' => $stockIn->id,
            'note' => $stockIn->note,
        ]);
        StockMutation::create([
            'product_id' => $stockOut->product_id,
            'type' => 'out',
            'quantity' => $stockOut->quantity,
            'transaction_date' => $stockOut->transaction_date,
            'reference_id' => $stockOut->id,
            'note' => $stockOut->note,
        ]);
        Stock::where('product_id', $stockIn->product_id)->update([
            'stock' => $stockIn->quantity,
        ]);
        Stock::where('product_id', $stockOut->product_id)->update([
            'stock' => $stockOut->quantity,
        ]);
    }

    /**
     * Handle the Stock "updated" event.
     */
    public function updated(Stock $stock): void
    {
        StockMutation::create([
            'product_id' => $stockOut->product_id,
            'type' => 'in',
            'quantity' => $stockOut->quantity,
            'transaction_date' => $stockOut->transaction_date,
            'reference_id' => $stockOut->id,
            'note' => $stockOut->note,
        ]);
        StockMutation::create([
            'product_id' => $stockIn->product_id,
            'type' => 'in',
            'quantity' => $stockIn->quantity,
            'transaction_date' => $stockIn->transaction_date,
            'reference_id' => $stockIn->id,
            'note' => $stockIn->note,
        ]);
        Stock::where('product_id', $stockOut->product_id)->update([
            'stock' => $stockOut->quantity,
        ]);
        Stock::where('product_id', $stockIn->product_id)->update([
            'stock' => $stockIn->quantity,
        ]);
        StockMutation::where('reference_id', $stockOut->id)->delete();
        StockMutation::where('reference_id', $stockIn->id)->delete();
    }

    /**
     * Handle the Stock "deleted" event.
     */
    public function deleted(Stock $stock): void
    {
        StockMutation::where('reference_id', $stock->id)->delete();
    }

    /**
     * Handle the Stock "restored" event.
     */
    public function restored(Stock $stock): void
    {
        //
    }

    /**
     * Handle the Stock "force deleted" event.
     */
    public function forceDeleted(Stock $stock): void
    {
        //
    }
}
