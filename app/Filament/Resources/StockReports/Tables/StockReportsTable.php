<?php

namespace App\Filament\Resources\StockReports\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StockReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
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
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->searchable()
                    ->sortable(),
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
                    ->relationship('category', 'name')
            ])
            ->headerActions([
                Action::make('export_pdf')
                    ->label('Export PDF / Cetak')
                    ->icon('heroicon-o-printer')
                    ->color('danger')
                    ->url(fn (Table $table) => route('admin.stock-reports.print', [
                        'category_id' => $table->getLivewire()->tableFilters['category_id']['value'] ?? null,
                        'search' => $table->getLivewire()->tableSearch ?? null,
                    ]), shouldOpenInNewTab: true),
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (Table $table) {
                        $recordsQuery = $table->getLivewire()->getFilteredTableQuery() ?? $table->getQuery();
                        
                        // We must eager load the relationships since we will use them in export
                        $records = $recordsQuery->with(['category', 'unit'])->get();
                        
                        $response = new StreamedResponse(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            
                            // UTF-8 BOM for Microsoft Excel compatibility
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
                            
                            // Write headers
                            fputcsv($handle, [
                                'SKU',
                                'Nama Produk',
                                'Kategori',
                                'Satuan',
                                'Stok Masuk',
                                'Stok Keluar',
                                'Sisa Stok',
                            ]);
                            
                            // Write rows
                            foreach ($records as $product) {
                                fputcsv($handle, [
                                    $product->sku,
                                    $product->name,
                                    $product->category?->name ?? '-',
                                    $product->unit?->name ?? '-',
                                    $product->total_stock_in ?? 0,
                                    $product->total_stock_out ?? 0,
                                    $product->stock,
                                ]);
                            }
                            
                            fclose($handle);
                        });
                        
                        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
                        $response->headers->set('Content-Disposition', 'attachment; filename="laporan_stok_' . now()->format('Ymd_His') . '.csv"');
                        
                        return $response;
                    })
            ])
            ->toolbarActions([]);
    }
}
