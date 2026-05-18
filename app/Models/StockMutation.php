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
        'note'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class);
    }   

    public function stockOut()
    {
        return $this->belongsTo(StockOut::class);
    }
}
