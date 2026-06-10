<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
        'product_id',
        'supplier_id',
        'quantity',
        'transaction_date',
        'note',
        'invoice_number',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function items()
    {
        return $this->hasMany(StockInItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'stock_in_items')
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function stockMutation()
    {
        return $this->hasOneThrough(
            StockMutation::class,
            StockInItem::class,
            'stock_in_id',
            'stock_in_item_id',
            'id',
            'id'
        )->where('stock_mutations.type', 'in');
    }

    public function stockMutations()
    {
        return $this->hasManyThrough(
            StockMutation::class,
            StockInItem::class,
            'stock_in_id',
            'stock_in_item_id',
            'id',
            'id'
        )->where('stock_mutations.type', 'in');
    }
}
