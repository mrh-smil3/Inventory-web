<?php

namespace App\Filament\Widgets;

use App\Filament\Support\InventoryDashboard;
use App\Models\StockMutation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StockMovementChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected ?string $heading = 'Pergerakan Stok (30 Hari Terakhir)';

    protected ?string $description = 'Total kuantitas barang masuk dan keluar per hari';

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '320px';

    protected function getData(): array
    {
        $days = InventoryDashboard::MOVEMENT_DAYS;
        $startDate = now()->subDays($days - 1)->startOfDay()->toDateString();
        $dateKeys = InventoryDashboard::lastDaysKeys($days);
        $labels = InventoryDashboard::lastDaysLabels($days);

        $incoming = StockMutation::query()
            ->where('type', 'in')
            ->where('transaction_date', '>=', $startDate)
            ->select('transaction_date', DB::raw('SUM(quantity) as total'))
            ->groupBy('transaction_date')
            ->pluck('total', 'transaction_date');

        $outgoing = StockMutation::query()
            ->where('type', 'out')
            ->where('transaction_date', '>=', $startDate)
            ->select('transaction_date', DB::raw('SUM(quantity) as total'))
            ->groupBy('transaction_date')
            ->pluck('total', 'transaction_date');

        $incomingData = collect($dateKeys)
            ->map(fn (string $date) => (int) ($incoming[$date] ?? 0))
            ->all();

        $outgoingData = collect($dateKeys)
            ->map(fn (string $date) => (int) ($outgoing[$date] ?? 0))
            ->all();

        return [
            'datasets' => [
                [
                    'label' => 'Barang Masuk',
                    'data' => $incomingData,
                    'borderColor' => '#22c55e',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Barang Keluar',
                    'data' => $outgoingData,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
