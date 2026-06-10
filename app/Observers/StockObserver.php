<?php

namespace App\Observers;

use App\Models\StockIn;
use App\Models\StockMutation;
use App\Models\StockOut;

class StockObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(mixed $model): void
    {
        //
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(mixed $model): void
    {
        if ($model instanceof StockIn) {
            foreach ($model->items as $item) {
                StockMutation::where('stock_in_item_id', $item->id)
                    ->where('type', 'in')
                    ->update([
                        'transaction_date' => $model->transaction_date,
                        'note' => $model->note,
                    ]);
            }
        } elseif ($model instanceof StockOut) {
            foreach ($model->items as $item) {
                StockMutation::where('stock_out_item_id', $item->id)
                    ->where('type', 'out')
                    ->update([
                        'transaction_date' => $model->transaction_date,
                        'note' => $model->note,
                    ]);
            }
        }
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(mixed $model): void
    {
        //
    }

    public function deleting(mixed $model): void
    {
        if ($model instanceof StockIn) {
            $model->items()->get()->each->delete();
        } elseif ($model instanceof StockOut) {
            $model->items()->get()->each->delete();
        }
    }
}
