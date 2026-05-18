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
        'unit',
        'category_id',
        'supplier_id',
        'slug',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }   

    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }   

    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }   

    public function stockMutations()
    {
        return $this->hasMany(StockMutation::class);
    }       
}
