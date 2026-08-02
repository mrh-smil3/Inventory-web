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
        'unit_id',
        'category_id',
        
    ];

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
