<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMutation extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'transaction_date',
        'reference_id',
        'stock_in_item_id',
        'stock_out_item_id',
        'note'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockInItem()
    {
        return $this->belongsTo(StockInItem::class);
    }

    public function stockOutItem()
    {
        return $this->belongsTo(StockOutItem::class);
    }
}
