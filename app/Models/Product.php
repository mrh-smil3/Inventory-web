<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'sku',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'low_stock_notified_at',
        'unit_id',
        'category_id',

    ];

    protected $casts = [
        'low_stock_notified_at' => 'datetime',
    ];

    public function isBelowLowStockThreshold(): bool
    {
        return $this->min_stock > 0 && $this->stock < ($this->min_stock * 2);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // public function supplier()
    // {
    //     return $this->belongsTo(Supplier::class);
    // }

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }

    public function stockInItems()
    {
        return $this->hasMany(StockInItem::class);
    }

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }

    public function stockOutItems()
    {
        return $this->hasMany(StockOutItem::class);
    }

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
