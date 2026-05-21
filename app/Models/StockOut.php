<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $fillable = [
        'product_id',
        'quantity',
        'transaction_date',
        'note',
        'invoice_number',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMutation()
    {
        return $this->hasOne(StockMutation::class);
    }
}
