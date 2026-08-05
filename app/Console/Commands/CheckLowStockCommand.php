<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Services\LowStockNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLowStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inventory:check-low-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim bell notification untuk produk yang stoknya di bawah 2x min_stock';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Product::where('min_stock', '>', 0)
            ->whereColumn('stock', '<', DB::raw('min_stock * 2'))
            ->whereNull('low_stock_notified_at')
            ->each(fn (Product $product) => LowStockNotifier::notify($product));

        Product::whereNotNull('low_stock_notified_at')
            ->whereColumn('stock', '>=', DB::raw('min_stock * 2'))
            ->update(['low_stock_notified_at' => null]);
    }
}
