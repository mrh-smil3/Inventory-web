<?php

namespace App\Filament\Widgets;

use App\Filament\Support\InventoryDashboard;
use App\Models\StockMutation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopOutboundProductsChart extends ChartWidget
{
    protected static ?int $sort = 5;

    protected ?string $heading = 'Produk Terlaris Keluar';

    protected ?string $description = '10 produk dengan kuantitas keluar tertinggi (30 hari)';

    protected ?string $maxHeight = '300px';

    protected string $color = 'danger';

    protected function getData(): array
    {
        $startDate = now()->subDays(InventoryDashboard::MOVEMENT_DAYS - 1)->startOfDay()->toDateString();

        $data = StockMutation::query()
            ->where('type', 'out')
            ->where('transaction_date', '>=', $startDate)
            ->join('products', 'products.id', '=', 'stock_mutations.product_id')
            ->select('products.name', DB::raw('SUM(stock_mutations.quantity) as total_out'))
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_out')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Kuantitas Keluar',
                    'data' => $data->pluck('total_out')->map(fn ($v) => (int) $v)->all(),
                    'backgroundColor' => '#ef4444',
                    'borderColor' => '#dc2626',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $data->pluck('name')->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
