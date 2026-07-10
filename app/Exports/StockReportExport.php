<?php

namespace App\Exports;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    public function query(): Builder
    {
        $stockInTotals = DB::table('stock_in_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_stock_in'))
            ->groupBy('product_id');

        $stockOutTotals = DB::table('stock_out_items')
            ->select('product_id', DB::raw('SUM(quantity) as total_stock_out'))
            ->groupBy('product_id');

        return Product::query()
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
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Produk',
            'Kategori',
            'Satuan',
            'Stok Masuk',
            'Stok Keluar',
            'Sisa Stok',
        ];
    }

    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->category?->name ?? '-',
            $product->unit?->name ?? '-',
            $product->total_stock_in ?? 0,
            $product->total_stock_out ?? 0,
            $product->stock,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
