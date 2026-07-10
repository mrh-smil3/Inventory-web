<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOutItem extends Model
{
    protected $fillable = [
        'stock_out_id',
        'product_id',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMutation()
    {
        return $this->hasOne(StockMutation::class)
            ->where('type', 'out');
    }
}
