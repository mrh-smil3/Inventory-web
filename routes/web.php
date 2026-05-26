<?php

use Illuminate\Support\Facades\Route;
use App\Models\Product;

Route::get('/', function () {
    return redirect('/admin');
});

Route::get('/admin/stock-reports/print', function () {
    $categoryId = request('category_id');
    $search = request('search');
    
    $query = Product::query()
        ->withSum('stockIns as total_stock_in', 'quantity')
        ->withSum('stockOuts as total_stock_out', 'quantity')
        ->with(['category', 'unit']);
        
    if ($categoryId) {
        $query->where('category_id', $categoryId);
    }
    
    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('sku', 'like', "%{$search}%");
        });
    }
    
    $products = $query->get();
    
    return view('print-stock-report', compact('products'));
})->name('admin.stock-reports.print')->middleware(['web', 'auth']);
