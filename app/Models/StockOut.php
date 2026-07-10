<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOut extends Model
{
    protected $fillable = [
        'product_id',
        'customer_name',
        'quantity',
        'transaction_date',
        'note',
        'invoice_number',
        'total_price',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function items()
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_out_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function stockMutation()
    {
        return $this->hasOneThrough(
            StockMutation::class,
            StockOutItem::class,
            'stock_out_id',
            'stock_out_item_id',
            'id',
            'id'
        )->where('stock_mutations.type', 'out');
    }

    public function stockMutations()
    {
        return $this->hasManyThrough(
            StockMutation::class,
            StockOutItem::class,
            'stock_out_id',
            'stock_out_item_id',
            'id',
            'id'
        )->where('stock_mutations.type', 'out');
    }
}
