<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\StockOut;

Route::get('/', function () {
    return redirect('/admin');
});

// Route::get('/report/stock/pdf', function () {
//     return app(ProductResource\Pages\ListProducts::class)->exportPdf();
// })->name('report.stock.pdf');

Route::get('/admin/stock-reports/print', function () {
    $categoryId = request('category_id');
    $search = request('search');
    
    $stockInTotals = DB::table('stock_in_items')
        ->select('product_id', DB::raw('SUM(quantity) as total_stock_in'))
        ->groupBy('product_id');

    $stockOutTotals = DB::table('stock_out_items')
        ->select('product_id', DB::raw('SUM(quantity) as total_stock_out'))
        ->groupBy('product_id');

    $query = Product::query()
        ->select('products.*')
        ->selectRaw('COALESCE(stock_in_totals.total_stock_in, 0) as total_stock_in')
        ->selectRaw('COALESCE(stock_out_totals.total_stock_out, 0) as total_stock_out')
        ->leftJoinSub($stockInTotals, 'stock_in_totals', function ($join) {
            $join->on('products.id', '=', 'stock_in_totals.product_id');
        })
        ->leftJoinSub($stockOutTotals, 'stock_out_totals', function ($join) {
            $join->on('products.id', '=', 'stock_out_totals.product_id');
        })
        ->with(['category:id,name', 'unit:id,name']);
        
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

Route::get('/admin/stock-outs/{stockOut}/print', function (StockOut $stockOut) {
    $stockOut->load([
        'items.product.unit',
        'items.product.category',
    ]);

    return view('print-stock-out', compact('stockOut'));
})->name('admin.stock-outs.print')->middleware(['web', 'auth']);
