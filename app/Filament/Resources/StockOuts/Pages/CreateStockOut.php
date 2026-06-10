<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockOut extends CreateRecord
{
    protected static string $resource = StockOutResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? null;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));

        return $data;
    }
}
