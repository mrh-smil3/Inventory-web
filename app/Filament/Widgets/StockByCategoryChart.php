<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class StockByCategoryChart extends ChartWidget
{
    use HasWidgetShield;
    protected static ?int $sort = 4;

    protected ?string $heading = 'Stok per Kategori';

    protected ?string $description = 'Distribusi unit stok berdasarkan kategori';

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = Product::query()
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.name', DB::raw('SUM(products.stock) as total_stock'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total_stock')
            ->limit(8)
            ->get();

        $colors = [
            '#f59e0b',
            '#3b82f6',
            '#22c55e',
            '#ef4444',
            '#8b5cf6',
            '#06b6d4',
            '#ec4899',
            '#64748b',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Unit Stok',
                    'data' => $data->pluck('total_stock')->map(fn ($v) => (int) $v)->all(),
                    'backgroundColor' => array_slice($colors, 0, $data->count()),
                ],
            ],
            'labels' => $data->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
