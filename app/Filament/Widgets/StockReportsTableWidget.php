<?php

namespace App\Filament\Widgets;

use App\Exports\StockReportExport;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockReportsTableWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Data Stok Barang';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->deferLoading()
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label('Kategori'),
                TextColumn::make('unit.name')
                    ->label('Satuan'),
                TextColumn::make('total_stock_in')
                    ->label('Stok Masuk')
                    ->numeric()
                    ->sortable()
                    ->default(0),
                TextColumn::make('total_stock_out')
                    ->label('Stok Keluar')
                    ->numeric()
                    ->sortable()
                    ->default(0),
                TextColumn::make('stock')
                    ->label('Sisa Stok')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
            ])
            ->headerActions([
                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn () => route('admin.stock-reports.print'))
                    ->openUrlInNewTab(),
                Action::make('exportXlsx')
                    ->label('Export XLSX')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn () => $this->exportXlsx()),
            ]);
    }

    public function getTableQuery(): Builder
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

    public function exportXlsx()
    {
        $filename = 'laporan_stok_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new StockReportExport, $filename);
    }

    public function previewPdf()
    {
        $query = $this->getFilteredTableQuery() ?? $this->getTableQuery();

        $products = (clone $query)
            ->with(['category:id,name', 'unit:id,name'])
            ->get();

        return view('print-stock-report-pdf', [
            'products' => $products,
        ]);
    }
}
